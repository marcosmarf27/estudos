<?php

use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;

/**
 * ProductCatalogView
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class ProductCatalogView extends TPage
{
    private $form, $cards, $pageNavigation;
    
    use Adianti\Base\AdiantiStandardCollectionTrait;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('samples');
        $this->setActiveRecord('Product');
        $this->addFilterField('description');
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_Product');
       // $this->form->setFormTitle(_t('Product cards'));
       // $this->form->addExpandButton();
        
        $description = new TEntry('description');
        TTransaction::open('samples');

        $products = Product::getIndexedArray('id', 'description');
        $description->setCompletion( array_values( $products ));
        $description->setSize('80%');
        $description->placeholder = 'Pesquise aqui...';

        TTransaction::close();
        $button = TButton::create('action1', [$this, 'onSearch'], 'Buscar', 'fa:search blue');
       $row =  $this->form->addFields(  [$description], [$button] );
       $row->layout = ['col-sm-10', 'col-sm-2' ];
      
       // $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
       // $this->form->addActionLink( 'Limpar pesquisa', new TAction([$this, 'onClear']), 'fa:eraser red');

        // keep the form filled with the search data
        $description->setValue( TSession::getValue( 'Product_description' ) );
        
        // creates a DataGrid
        $this->cards = new TCardView;
		//$this->cards->setContentHeight(170);
		//$this->cards->setTitleAttribute('description');
        $this->cards->setUseButton();
		
		$this->setCollectionObject($this->cards);
		
		$this->cards->setItemTemplate('<div style="float:left;width:50%;padding-right:10px">
		                                    <br>° {description} <br>
                                            <hr>
		                                  
		                                    <br>   <div id = "preco">R$  {sale_price} </div>
		                               </div>
		                               <div style="float:right;width:50%">
		                                   <img style="height:100px;float:right;margin:5px" src="{photo_path}">
		                               </div> ');
        
		$this->cards->addAction(new TAction([$this, 'onSelect'], ['id' => '{id}']),  'Adicionar', 'fa:plus white');
		
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        
        // creates the page structure using a table
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
       // $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form); // add a row to the form
        $vbox->add(TPanelGroup::pack('', $this->cards, $this->pageNavigation)); // add a row for page navigation
        
        // add the table inside the page
        parent::add($vbox);
    }
    
    /**
     * Select product
     */
    public static function onSelect( $param )
    {
        $cart_items = TSession::getValue('cart_items');
        
        if (isset($cart_items[ $param['id'] ]))
        {
            $cart_items[ $param['id'] ] ++;
        }
        else
        {
            $cart_items[ $param['id'] ] = 1;
        }
        
        ksort($cart_items);
        
        TSession::setValue('cart_items', $cart_items);

        $itens = count($cart_items);

       // TSession::freeSession();

     /*    echo '<pre>';

        print_r($cart_items);
        print_r(count($cart_items));


        echo '</pre>'; */
        TScript::create("$( '#carrinho' ).html( '{$itens}' );");
        AdiantiCoreApplication::loadPage('CartManagementView', 'onReload', [ 'register_state' => 'false']);
    }

    public function onClear()
    {
      
      $this->onSearch();
    }
}
