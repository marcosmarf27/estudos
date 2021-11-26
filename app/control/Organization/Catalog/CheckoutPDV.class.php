<?php

use Adianti\Control\TAction;
use Adianti\Control\TWindow;
use Adianti\Widget\Form\TRadioGroup;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Wrapper\BootstrapFormBuilder;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Wrapper\BootstrapDatagridWrapper;

/**
 * FormShowHideRowsView
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class CheckoutPDV extends TWindow
{
    private $form;
    private $detail_list;
    
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();
        parent::setSize(0.5, null);
        parent::removePadding();
        parent::setTitle('Fechar Pedido');
        parent::disableEscape();

        if(parent::isMobile()){

            parent::setSize(0.9, null);



        }
        
        // create the form
        $this->form = new BootstrapFormBuilder('form_checkout');
       // $this->form->setFormTitle('Fechar Pedido');
        
        // create the form fields
        $type        = new TRadioGroup('type');
        $payment       = new TRadioGroup('payment');
        $payment->setLayout('horizontal');
        $payment->setUseButton();
      // $payment->setSize('100%');
        $payment->setBreakItems(2);

        $combo_pay = array();
        $combo_pay['1'] ='<i class="fas fa-money-bill-wave"></i> Dinheiro';
        $combo_pay['2'] ='<i class="fas fa-credit-card"></i> Cartão Crédito';
        $combo_pay['3'] ='<i class="far fa-credit-card"></i> cartão Débito';
        $combo_pay['4'] ='<i class="fas fa-file-invoice-dollar"></i> Pix/Transferência';
        $payment->addItems($combo_pay);

        foreach ($payment->getLabels() as $key => $label)
        {
            $label->setTip("Pague com $key");
            $label->setSize(130);
        }
        

    
       
       
        
        $type->setChangeAction(new TAction(array($this, 'onChangeType')));
        $combo_items = array();
        $combo_items['1'] ='Avulso';
        $combo_items['2'] ='Mesa/Comanda';
      
        $type->addItems($combo_items);
        $type->setLayout('horizontal');
        $type->setUseButton();
        $type->setSize('100%');
        
        // default value
        $type->setValue('1');
        
        // fire change event
        self::onChangeType( ['type' => '1'] );
        
        // add the fields inside the form
        $this->form->addFields( [$type] );
      



        //detalhes do item comprados

        $this->detail_list = new BootstrapDatagridWrapper( new TDataGrid );
        $this->detail_list->style = 'width:100%';
        $this->detail_list->disableDefaultClick();
        
        $product       = new TDataGridColumn('description',  'Desc', 'left');
        $price         = new TDataGridColumn('sale_price',  'valor',    'right');
        $amount        = new TDataGridColumn('amount',  'qtd',    'center');
       
      //  $total         = new TDataGridColumn('total',  'Total',    'right');
        
        $this->detail_list->addColumn( $product );
        $this->detail_list->addColumn( $amount );
        $this->detail_list->addColumn( $price );
      
       
      //  $this->detail_list->addColumn( $total );
        
        $format_value = function($value) {
            if (is_numeric($value)) {
                return 'R$ '.number_format($value, 2, ',', '.');
            }
            return $value;
        };
        
        $price->setTransformer($format_value);
        
        // define totals
        $price->setTotalFunction( function($values) {
            return array_sum((array) $values);
        });
        
        $this->detail_list->createModel();
        $cart_items = TSession::getValue('cart_items');

        if($cart_items){

            
            TTransaction::open('samples');
            $this->detail_list->clear();
            foreach ($cart_items as $id => $amount)
            {
                $product = new Product($id);
                
                $item = new StdClass;
               // $item->id          = $product->id;
                $item->description = $product->description;
                $item->amount      = $amount;
                $item->sale_price  = $amount * $product->sale_price;
                
                $this->detail_list->addItem( $item );
            }
            TTransaction::close();


        }
       
      
        
        $panel = new TPanelGroup('', '#f5f5f5');
        $panel->add($this->detail_list);
        $panel->{'name'} = 'itens';
        $panel->getBody()->style = 'overflow-x:auto';
        
         $this->form->addContent([$panel]);

         $this->form->addFields( [$payment] );

         $this->form->addAction('Registrar', new TAction(array($this, 'registrar')), 'fas: fa-cash-register green');

         $dropdown = new TDropDown('Opções', 'fa:th blue');
        $dropdown->addPostAction( 'Imprimir', new TAction(array($this, 'imprimir') ), $this->form->getName(), 'far:file-pdf');
        //$dropdown->addAction( 'Shortcut to customers', new TAction(array('CustomerDataGridView', 'onReload') ), 'fa:link');
        $this->form->addFooterWidget($dropdown);

         
       
       
   
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        //$vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);

        parent::add($vbox);
    }
    
    /**
     * Event executed when type is changed
     */
    public static function onChangeType($param)
    {
        if ($param['type'] == '1')
        {
            TQuickForm::showField('form_checkout', 'itens');
        
          //  TQuickForm::hideField('form_checkout', 'itens');
         
        }
        else
        {
            TQuickForm::hideField('form_checkout', 'itens');
       
        }
    }

    public function registrar($param){

        
        
    }

    public function imprimir($param){


        
        TTransaction::open('samples');

        
             
              
        $html = new THtmlRenderer('app/resources/modelo_avulso.html');
        $html->disableHtmlConversion();

        $replace = array();
        $replace['tipo']    =  'Avulso';
      
        $replace['data']    =  '21-09-2021 09:30';
        $replace['usuario']    = 'Jaqueline' ;
        $replace['usuario']    =  'Jaqueline';
        $replace['empresa']    =  'Pizza Adianti';
        $replace['mesa']    =  '12';
      $replace['obs']    = 'Não colocar ketchup, e colocar bastante batata frita em cima do hamburguer';
      //  $replace['client_email']    =  'marcosmarf27@gmail.com';
        $replace['total']    =  '64.58';

        $html->enableSection('main', $replace);

    
   

       // $itens = ItemDelivery::where('pedido_delivery_id', '=', $object->id)->load();
        $replace = array();

      /*   foreach($itens as $item){


            $jsonitem = json_decode($item->opcoes);
            $opformatada = '';
            foreach($jsonitem as $ob){

            $opformatada .= strtolower( $ob->group_name . ': '. $ob->name . PHP_EOL);

                     }
                            $replace[] = array('item' => $item->name,
                                                'opcao'=> $opformatada);

            } */
        

      

    
    // define with sections will be enabled

    $replace[] = array('desc' => 'Cerveja heineken',
    'qtd'=> '1', 'valor'=> '12.0');
    $replace[] = array('desc' => 'Esfiha chocolate',
    'qtd'=> '2', 'valor'=> '7.0');

   

        $html->enableSection('itens', $replace, TRUE);


        $msg = $html->getContents();
        //$msg2 = urlencode($msg);

          //  $html = clone $this->datagrid;
            $contents = file_get_contents('app/resources/styles-print.html') . $html->getContents();
           // $impressao = urlencode($contents);
            // converts the HTML template into PDF
           
            
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->getOptions()->setChroot("app/images"); 
            $dompdf->loadHtml($contents);
            $customPaper = array(0,0,280,560);
            $dompdf->setPaper($customPaper, 'portrait');
            $dompdf->render();
            
            $file = 'app/output/datagrid-export.pdf';
            
            // write and open file
            file_put_contents($file, $dompdf->output());
            
            $window = TWindow::create('Impressão', 0.8, 0.8);
            $object = new TElement('object');
            $object->data  = $file;
            $object->type  = 'application/pdf';
            $object->style = "width: 100%; height:calc(100% - 10px)";
            $window->add($object);
            $window->show();

        TTransaction::close();

        TWindow::closeWindowByName('CheckoutPDV');
    }

    
}
