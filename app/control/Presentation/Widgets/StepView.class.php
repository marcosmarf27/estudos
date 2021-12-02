<?php
/**
 * StepView
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class StepView extends TPage
{
    private $step;
    
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();
        
        $this->step = new TPageStep;
        $this->step->addItem('Cadastro', new TAction(['CustomerDataGridView', 'onReload']), '<i class="fas fa-address-card"></i>');
        $this->step->addItem('Endereço','', '<i class="fas fa-map-marked-alt"></i>');
        $this->step->addItem('Pagamento', '', '<i class="far fa-credit-card"></i>');
       $this->step->select('Endereço');
        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->step);
        
        parent::add($vbox);
    }
}
