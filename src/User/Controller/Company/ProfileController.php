<?php
namespace User\Controller\Company;

use Application\Entity\Request;
use Application\Entity\Requests;
use Ellie\UI\Element\Select;
use Ellie\UI\Form;
use Ellie\UI\Element\TreeSelect;
use Ellie\UI\Element\Button;
use Ellie\UI\Element\Text;
use Ellie\UI\Element\Textarea;
use Ellie\UI\Element\CheckBox;
use Ellie\UI\Set\TabSet;
use Ellie\UI\Set\FieldSet;

use Application\Controller\BaseController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ProfileController extends BaseController{
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


    public function editAction()
    {
        if($this->request->isPost()){

            $request = new Request;

            $request->setTitle($this->request->getPost()['title']);
            $request->setType($this->request->getPost()['type']);
            $request->setDescription($this->request->getPost()['description']);
            $user = $this->doctrineService->find('Application\Entity\User', 1);

            $request->setUser($user);
            $service = $this->doctrineService->find('Application\Entity\Service', 12);
            $request->setService($service);
            $this->doctrineService->persist($request);

            $this->doctrineService->flush();
            var_dump(1);die;

        }
        $services = $this->serviceUiGeneratorPlugin->getForTree($this->language->getId());
        return $this->getCreateServiceForm($services,$this->language->getCode());
    }

    public function getCreateServiceForm($services ,$languageCode, $currentService= null)
    {

        $header = (isset($currentService))?"Edit Service":$this->translator->translate("Account Management");
        $action = (isset($currentService))?"edit":"create";
        $id = (isset($currentService))?$currentService[0]['id']:null;
        $serviceLangs = (isset($currentService))?(($currentService[0]["code"]=="fa")?array("fa"=>$currentService[0],"en"=>$currentService[1]):array("fa"=>$currentService[1],"en"=>$currentService[0])):array();
        $form     = new Form(['header' => $header,'action' => $this->url()->getController()->getRequest()->getBaseUrl(). "/$this->lang/user/b2b/create",'name'=>'serviceForm']);

        $tab = new TabSet();

        $fieldsetFa = new FieldSet(['name' => 'serviceFa','header' => $this->translator->translate('Edit Profile') , 'label' => 'Request']);
        $serviceNameFa = new Text([
            'name' => 'title',
            'placeholder' => $this->translator->translate('Title'),
            'type' => 'text',
            'value' => (isset($serviceLangs["fa"]["name"]))?$serviceLangs["fa"]["name"]:"",
            'label' => $this->translator->translate('Title'),
        ]);

        $descriptionFa = new Textarea([
            'name' => 'description',
            'placeholder' => $this->translator->translate('Description') .' ...',
            'label' => $this->translator->translate('Description'),
            'value'=>(isset($serviceLangs["fa"]["description"]))?$serviceLangs["fa"]["description"]:"",
        ]);

        $enablCheckboxFa = new Select(['name' => 'type', 'label' => $this->translator->translate('Type'),
            'options'=>['I Want' => $this->translator->translate('I Want'),
                'I Have' => $this->translator->translate('I Have')]]);

        $fieldsetFa->addChild($serviceNameFa, 'serviceNameFa');
        $fieldsetFa->addChild($descriptionFa, 'username');
        $fieldsetFa->addChild($enablCheckboxFa);

        $submit = new Button();

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
}