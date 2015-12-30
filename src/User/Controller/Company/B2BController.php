<?php
namespace User\Controller\Company;

use Application\Entity\BuyerRequest;
use Application\Entity\Request;
use Application\Entity\SellerRequest;
use Ellie\UI\Element\Date;
use Ellie\UI\Element\File;
use Ellie\UI\Form;
use Ellie\UI\Element\TreeSelect;
use Ellie\UI\Element\Button;
use Ellie\UI\Element\Text;
use Ellie\UI\Element\Textarea;
use Ellie\UI\Set\TabSet;
use Ellie\UI\Set\FieldSet;

use Application\Controller\BaseController;
use Zend\File\Transfer\Adapter\Http;
use Zend\Validator\File\Extension;
use Zend\Validator\File\Size;
use Zend\View\Model\ViewModel;

class B2BController extends BaseController{
    protected $serviceQueryPlugin;
    protected $serviceUiGeneratorPlugin;
    protected $language;
    protected $doctrineService;

    public function __construct($services,$eventHandler)
    {
        $this->doctrineService = $services["doctrine"];
        $this->request = $this->getRequest();
        $this->eventHandler = $eventHandler;

    }

    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $this->serviceQueryPlugin = $this->ServiceQuery();
        $this->serviceUiGeneratorPlugin = $this->ServiceUiGenerator();
        $languageCode = $this->params()->fromRoute('lang', 'fa');
        $this->language = $this->doctrineService->getRepository('Application\Entity\Language')->findOneBy(array("code"=> $languageCode));
        return parent::onDispatch($e);

    }

    public function listAction()
    {
        $user = $this->getServiceLocator()->get('Ellie\Service\Authentication')->getIdentity();
        $userId = $user->getId();
        $queryBuilder = $this->doctrineService->createQueryBuilder();
        if($user->getRole()->getName() == 'Company'){
            $user = "company";

            $queryBuilder
                ->select('r.id', 'r.title' , 'r.description' , 'r.type', 'r.createdAt')
                ->from('Application\Entity\Request','r')
                ->where('r.user_id = :id')
                ->setParameters(array('id'=>$userId));
        }else{
            $queryBuilder
                ->select('r.id', 'r.title' , 'r.description' , 'r.type', 'r.createdAt')
                ->from('Application\Entity\Request','r');
            $user = "admin";
        }
        $requests = $queryBuilder->getQuery()->getResult();
        $view = new ViewModel();
        $view->setTemplate('user/request');
        $view->setVariables(['requests' => $requests, 'lang' => $this->lang, 'user' => $user]);
        return $view;
    }

    public function createAction()
    {
        $this->layout()->message = [
            'type' => 'danger',
            'content' => 'این صفحه به صورت موقت طراحی شده و پس از کامل شدن صفحه ی گرافیکی عرضه ها و تقاضاها جایگزین خواهد شد'
        ];
        if($this->request->isPost()){
            $request = new Request;
            $files =  $this->request->getFiles()->toArray();
            $httpadapter = new Http();
            $filesize  = new Size(array('min' => 1000 )); //1KB
            $extension = new Extension(array('extension' => array('txt')));
            $httpadapter->setValidators(array($filesize, $extension), $files);
            if($httpadapter->isValid()) {
                $httpadapter->setDestination( getcwd().'/public/files/');

                if($httpadapter->receive($files['title']['name'])) {
                    var_dump($files['title']['name']);die();

                    $newfile = $httpadapter->getFileName();
                    var_dump($newfile);die();

                }
                var_dump($files['title']['name']);die();
            }
            var_dump(2);die();
            try{
                $request->setTitle($this->request->getPost()['title']);
                $request->setType($this->request->getPost()['type']);
                $request->setDescription($this->request->getPost()['description']);
                $user = $this->doctrineService->find('Application\Entity\User', 1);
                $request->setUser($user);
                $service = $this->doctrineService->find('Application\Entity\Service', 12);
                $request->setService($service);
                $this->doctrineService->persist($request);
                $this->doctrineService->flush();
            }


            catch(\Exception $e){
                var_dump($e->getMessage());die;
            }
        }
        $services = $this->serviceUiGeneratorPlugin->getForTree($this->language->getId());
        return $this->getCreateServiceForm($services,$this->language->getCode());
    }

    public function requestdetailAction()
    {
        $user = $this->getServiceLocator()->get('Ellie\Service\Authentication')->getIdentity();
        $queryBuilder = $this->doctrineService->createQueryBuilder();

        if($user->getRole()->getName() == 'Company'){
            $user = "company";

        }else{
            $user = "admin";
        }
        $request = $this->doctrineService->find('Application\Entity\Request', $this->params('id'));

//        $queryBuilder
//            ->select('r.id')
//            ->from('Application\Entity\Request','r')
//            ->join('Application\Entity\RequestService', 'rs', 'r.id = rs.request_id')
//            ->where('rs.service_id = :id')
//            ->andWhere('r.type = :type')
//            ->setParameters(['id'=> $request->getService()[0]->getId(), 'type' => "Buy"])
//        ;
//        $requests = $queryBuilder->getQuery()->getResult();
//        echo "<pre>";
        $view = new ViewModel();
        $view->setTemplate('user/b2b/request-detail');
        $view->setVariables(['request' => $request, 'lang' => $this->lang, 'user' => $user]);
        return $view;
    }

    public function buyrequestAction()
    {
        if($this->request->isPost()){
            $request = new Request;
            $buyerRequest = new BuyerRequest;
            $files =  $this->request->getFiles()->toArray();
            $file = new File(['name' => 'allow_i']);
            $file->setValue($files);

            try{
                $userId = $this->getServiceLocator()->get('Ellie\Service\Authentication')->getIdentity()->getId();
                $request->setTitle($this->request->getPost()['title']);
                $request->setType('Buy');
                $request->setDescription($this->request->getPost()['description']);
                $user = $this->doctrineService->find('Application\Entity\User', $userId);
                $request->setUser($user);
                $service = $this->doctrineService->find('Application\Entity\Service', $this->request->getPost()['parent']);
                $request->setService($service);

                $buyerRequest->setAllowI($file->getValue());
                $buyerRequest->setProposedPrice($this->request->getPost()['price']);
                $buyerRequest->setExpireDate(new \DateTime($this->request->getPost()['expireDate']));
                $buyerRequest->setDeadline(new \DateTime($this->request->getPost()['deadline']));
                $buyerRequest->setNumber($this->request->getPost()['number']);
                $buyerRequest->setUnit($this->request->getPost()['unit']);
                $buyerRequest->setRequest($request);
                $this->doctrineService->persist($request);
                $this->doctrineService->persist($buyerRequest);

                $this->doctrineService->flush();
            }

            catch(\Exception $e){
                var_dump($e->getMessage());die;
            }
        }
        $services = $this->serviceUiGeneratorPlugin->getForTree($this->language->getId());
        return $this->getCreateBuyForm($services,$this->language->getCode());
    }

    public function sellrequestAction()
    {
        if($this->request->isPost()){
            echo "<pre>";
            $request = new Request;
            $sellerRequest = new SellerRequest;
            $files =  $this->request->getFiles()->toArray();

            $tech = array_shift($files);
            $file = new File(['name' => 'files']);
            $file->setValue(['files' => $files]);



            try{
                $userId = $this->getServiceLocator()->get('Ellie\Service\Authentication')->getIdentity()->getId();

                $request->setTitle($this->request->getPost()['title']);
                $request->setType($this->request->getPost()['type']);
                $request->setDescription($this->request->getPost()['description']);
                $user = $this->doctrineService->find('Application\Entity\User', $userId);
                $request->setUser($user);
                $service = $this->doctrineService->find('Application\Entity\Service', $this->request->getPost()['parent']);
                $request->setService($service);
                $request->setType('Sell');

                $sellerRequest->setImage($file->getValue()['image']);
                $sellerRequest->setTechnicalReport($file->getValue()['technical_report']);
                $sellerRequest->setPrice($this->request->getPost()['price']);
                $sellerRequest->setDeadline(new \DateTime($this->request->getPost()['deadline']));
                $sellerRequest->setNumber($this->request->getPost()['number']);
                $sellerRequest->setUnit($this->request->getPost()['unit']);
                $sellerRequest->setRequest($request);

                $this->doctrineService->persist($request);
                $this->doctrineService->persist($sellerRequest);
                $this->doctrineService->flush();


            }


            catch(\Exception $e){
                var_dump($e->getMessage());die;
            }
        }
        $services = $this->serviceUiGeneratorPlugin->getForTree($this->language->getId());
        return $this->getCreateSellForm($services,$this->language->getCode());
    }

    public function getCreateBuyForm($services ,$languageCode, $currentService= null)
    {

        $header = (isset($currentService))?"Edit Service":$this->translator->translate("Business Management");
        $action = (isset($currentService))?"edit":"create";
        $id = (isset($currentService))?$currentService[0]['id']:null;
        $serviceLangs = (isset($currentService))?(($currentService[0]["code"]=="fa")?array("fa"=>$currentService[0],"en"=>$currentService[1]):array("fa"=>$currentService[1],"en"=>$currentService[0])):array();
        $form     = new Form(['header' => $header,
            'action' => $this->url()->getController()->getRequest()->getBaseUrl(). "/$this->lang/user/b2b/buyrequest",
            'name'=>'serviceForm']);

        $tab = new TabSet();

        $fieldsetFa = new FieldSet(['name' => 'serviceFa','header' => $this->translator->translate('Create New Request') , 'label' => 'Request']);

        $serviceNameFa = new Text([
            'name' => 'title',
            'placeholder' => $this->translator->translate('Title'),
            'type' => 'text',
            'value' => (isset($serviceLangs["fa"]["name"]))?$serviceLangs["fa"]["name"]:"",
            'label' => $this->translator->translate('Title'),
        ]);

        $priceText = new Text([
            'name' => 'price',
            'placeholder' => $this->translator->translate('000.000'),
            'type' => 'text',
            'value' => "",
            'label' => $this->translator->translate('Price'),
        ]);

        $numberText = new Text([
            'name' => 'number',
            'placeholder' => $this->translator->translate('0'),
            'type' => 'text',
            'value' => "",
            'label' => $this->translator->translate('Number'),
        ]);

        $unitText = new Text([
            'name' => 'unit',
            'placeholder' => $this->translator->translate('KG'),
            'type' => 'text',
            'value' => "",
            'label' => $this->translator->translate('Unit'),
        ]);


        $descriptionFa = new Textarea([
            'name' => 'description',
            'placeholder' => $this->translator->translate('Description') .' ...',
            'label' => $this->translator->translate('Description'),
            'value'=>(isset($serviceLangs["fa"]["description"]))?$serviceLangs["fa"]["description"]:"",
        ]);


        $alloIFa = new File([
            'name' => 'allow_i',
            'value' => '',
            'label' => $this->translator->translate('Allow I'),
        ]);

        $deadlineDate = new Date([
            'name' => 'deadline',
            'value' => '',
            'label' => $this->translator->translate('Deadline'),
        ]);


        $expireDate = new Date([
            'name' => 'expireDate',
            'value' => '',
            'label' => $this->translator->translate('Expire Date Request'),
        ]);

        $fieldsetFa->addChild($serviceNameFa, 'serviceNameFa');
        $fieldsetFa->addChild($alloIFa, 'serviceNameFa');
        $fieldsetFa->addChild($priceText);
        $fieldsetFa->addChild($numberText);
        $fieldsetFa->addChild($unitText);
        $fieldsetFa->addChild($deadlineDate);
        $fieldsetFa->addChild($expireDate);
        $fieldsetFa->addChild($descriptionFa, 'username');

        $submit = new Button(['value' => 'Submit']);

        $treeSelect = new TreeSelect([
            "title"=> $this->translator->translate("choose category of your service"),
            "services"=>$services,
            "selected"=>(isset($currentService[0]["parent"]))?$currentService[0]["parent"]:"",
            "name" => "parent",
        ]);
        $fieldsetFa->addChild($treeSelect);


        $tab->addChild($fieldsetFa, 'fieldsetFa');
        $form->addChild($tab);
        $form->addChild($submit, 'submit');
        return $form;

    }

    public function getCreateSellForm($services ,$languageCode, $currentService= null)
    {

        $header = (isset($currentService))?"Edit Service":$this->translator->translate("Business Management");
        $action = (isset($currentService))?"edit":"create";
        $id = (isset($currentService))?$currentService[0]['id']:null;
        $serviceLangs = (isset($currentService))?(($currentService[0]["code"]=="fa")?array("fa"=>$currentService[0],"en"=>$currentService[1]):array("fa"=>$currentService[1],"en"=>$currentService[0])):array();
        $form     = new Form(['header' => $header,
            'action' => $this->url()->getController()->getRequest()->getBaseUrl(). "/$this->lang/user/b2b/sellrequest",
            'name'=>'serviceForm']);

        $tab = new TabSet();

        $fieldsetFa = new FieldSet(['name' => 'serviceFa','header' => $this->translator->translate('Create New Sell Request') , 'label' => 'Request']);

        $serviceNameFa = new Text([
            'name' => 'title',
            'placeholder' => $this->translator->translate('Title'),
            'type' => 'text',
            'value' => (isset($serviceLangs["fa"]["name"]))?$serviceLangs["fa"]["name"]:"",
            'label' => $this->translator->translate('Title'),
        ]);

        $priceText = new Text([
            'name' => 'price',
            'placeholder' => $this->translator->translate('000.000'),
            'type' => 'text',
            'value' => "",
            'label' => $this->translator->translate('Price'),
        ]);

        $numberText = new Text([
            'name' => 'number',
            'placeholder' => $this->translator->translate('0'),
            'type' => 'text',
            'value' => "",
            'label' => $this->translator->translate('Number'),
        ]);

        $unitText = new Text([
            'name' => 'unit',
            'placeholder' => $this->translator->translate('KG'),
            'type' => 'text',
            'value' => "",
            'label' => $this->translator->translate('Unit'),
        ]);


        $descriptionFa = new Textarea([
            'name' => 'description',
            'placeholder' => $this->translator->translate('Description') .' ...',
            'label' => $this->translator->translate('Description'),
            'value'=>(isset($serviceLangs["fa"]["description"]))?$serviceLangs["fa"]["description"]:"",
        ]);


        $alloIFa = new File([
            'name' => 'technical_report',
            'value' => '',
            'label' => $this->translator->translate('Technical Report'),
        ]);

        $imageFile = new File([
            'name' => 'image',
            'value' => '',
            'label' => $this->translator->translate('Image'),
        ]);

        $expireDate = new Date([
            'name' => 'expireDate',
            'value' => '',
            'label' => $this->translator->translate('Expire Time'),
        ]);




        $fieldsetFa->addChild($serviceNameFa, 'serviceNameFa');
        $fieldsetFa->addChild($priceText);
        $fieldsetFa->addChild($numberText);
        $fieldsetFa->addChild($unitText);
        $fieldsetFa->addChild($alloIFa, 'serviceNameFa');
        $fieldsetFa->addChild($imageFile);
        $fieldsetFa->addChild($expireDate);
        $fieldsetFa->addChild($descriptionFa, 'username');

        $submit = new Button(['value' => 'Submit']);

        $treeSelect = new TreeSelect([
            "title"=> $this->translator->translate("choose category of your service"),
            "services"=>$services,
            "selected"=>(isset($currentService[0]["parent"]))?$currentService[0]["parent"]:"",
            "name" => "parent",
        ]);
        $fieldsetFa->addChild($treeSelect);


        $tab->addChild($fieldsetFa, 'fieldsetFa');
        $form->addChild($tab);
        $form->addChild($submit, 'submit');
        return $form;

    }

    public function indexAction()
    {
        if($this->params('id') == "buy"){
            $type = "Buy";
        }else{
            $type = "Sell";
        }
        $requests = $this->doctrineService->getRepository('Application\Entity\Request')->findBy(['type' => $type]);
        $view = new ViewModel();
        $view->setTemplate('user/b2b/index');
        $view->setVariables(['requests' => $requests, 'lang' => $this->lang]);
        return $view;
    }

}