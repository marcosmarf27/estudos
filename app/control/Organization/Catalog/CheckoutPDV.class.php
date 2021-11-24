<?php

use Adianti\Control\TWindow;
use Adianti\Widget\Form\TRadioGroup;

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
        if ($param['type'] == 'p')
        {
            TQuickForm::showField('form_show_hide', 'item_price');
            TQuickForm::showField('form_show_hide', 'units');
            TQuickForm::hideField('form_show_hide', 'hour_price');
            TQuickForm::hideField('form_show_hide', 'hours');
        }
        else
        {
            TQuickForm::hideField('form_show_hide', 'item_price');
            TQuickForm::hideField('form_show_hide', 'units');
            TQuickForm::showField('form_show_hide', 'hour_price');
            TQuickForm::showField('form_show_hide', 'hours');
        }
    }
}
