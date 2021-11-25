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
       
        
        $type->setChangeAction(new TAction(array($this, 'onChangeType')));
        $combo_items = array();
        $combo_items['1'] ='Avulso';
        $combo_items['2'] ='Abrir Mesa/Comanda';
      
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
      
        
        $panel = new TPanelGroup('', '#f5f5f5');
        $panel->add($this->detail_list);
        $panel->{'name'} = 'itens';
        $panel->getBody()->style = 'overflow-x:auto';
        
         $this->form->addContent([$panel]);
       
   
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
}
