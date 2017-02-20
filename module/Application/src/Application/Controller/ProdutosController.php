<?php

namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Application\Form\ProdutoForm;
use Application\Model\Produto;
use Zend\Form\Element\File;
use Zend\Validator\File\Size;
use Zend\File\Transfer\Adapter\Http;

class ProdutosController extends AbstractActionController
{
    protected $produtoTable;

    public function getProdutoTable(){
        if (!$this->produtoTable){
            $sm = $this->getServiceLocator();
            $this->produtoTable = $sm->get('produto_table');
        }
        
        return $this->produtoTable;
    }
    
    public function indexAction(){
        $messages = $this->flashMessenger()->getMessages();
        $pageNumber = (int) $this->params()->fromQuery('pagina');
        
        if($pageNumber == 0){
            $pageNumber = 1;
        }
        
        $produtos = $this->getProdutoTable()->fetchAll($pageNumber, 2);
        return new ViewModel(array(
            'messages' => $messages,
            'produtos' => $produtos,
            'titulo' => 'Listagem de produtos'
        ));
    }
    
    public function cadastrarAction(){
        $form = new ProdutoForm();
        
        $request = $this->getRequest();
        
        if ($request->isPost()){
            // instanciando o model produto
            $produto = new Produto();
            
            $nonFile = $request->getPost()->toArray();
            $File = $this->params()->fromFiles('produto_foto');
            
            if($File['name'] == ""){
            	$filename = $produto->produto_foto;
            }else{
            	$filename = $File['name'];
            }
            
            // pegando os dados postados            
            $data = $request->getPost();
            
            $form->setInputFilter($produto->getInputFilter());
            $form->setData($data);
            
            if($form->isValid()){
            	if($File['name'] != ""){
            		$size = new Size(array('max' => 2000000));
            		$adapter = new Http();
            		$adapter->setValidators(array($size), $File['name']);
            	
            		if(!$adapter->isValid()){
            			$dataError = $adapter->getMessages();
            			$error = array();
            			foreach ($dataError as $row){
            				$error[] = $row;
            			}
            			$form->setMessages(array('produto_foto' => $error));
            		}else{
            			$diretorio = $request->getServer()->DOCUMENT_ROOT.'/projetozend/public/conteudos/produtos';
            			$adapter->setDestination($diretorio);
            	
            			if($adapter->receive($File['name'])){
            				$this->flashmessenger()->addMessage(array('success' => 'Imagem enviada com sucesso.'));
            			}else{
            				$this->flashmessenger()->addMessage(array('error' => 'Imagem não enviada.'));
            			}
            		}
            	}
            	
                $produto->exchangeArray($data);
                $produto->produto_foto = $File['name'];
                $this->getProdutoTable()->saveProduto($produto);
                
                $this->flashMessenger()->addMessage(array('success' => 'Cadastro efetuado com sucesso'));
                $this->redirect()->toUrl('index');
            }
            
        }
        
        $view = new ViewModel(array(
            'form' => $form
        ));
        $view->setTemplate('application/produtos/form.phtml');
        
        return $view;
    }
    
    public function editarAction(){
        $id = $this->params('id');
        
        $produto = $this->getProdutoTable()->getProduto($id);

        $form = new ProdutoForm();
        $form->setBindOnValidate(false);
        $form->bind($produto);
        $form->get('submit')->setLabel('Alterar');
        
        $request = $this->getRequest();
                
        if($request->isPost()){
            
            $nonFile = $request->getPost()->toArray();
            $File = $this->params()->fromFiles('produto_foto');
            
            if($File['name'] == ""){
            	$filename = $produto->produto_foto;
            }else{
            	$filename = $File['name'];
            }
            $data = array_merge($nonFile,array('produto_foto' => $filename));
            $form->setData($data);
            
            if($form->isValid()){
                $form->bindValues();
                if($File['name'] != ""){
                    $size = new Size(array('max' => 2000000));
                    $adapter = new Http();
                    $adapter->setValidators(array($size), $File['name']);
                    
                    if(!$adapter->isValid()){
                        $dataError = $adapter->getMessages();
                        $error = array();
                        foreach ($dataError as $row){
                            $error[] = $row;
                        }
                        $form->setMessages(array('produto_foto' => $error));
                    }else{
                        $diretorio = $request->getServer()->DOCUMENT_ROOT.'/projetozend/public/conteudos/produtos';
                        $adapter->setDestination($diretorio);
                        
                        if($adapter->receive($File['name'])){
                            $this->flashmessenger()->addMessage(array('success' => 'Imagem enviada com sucesso.'));
                        }else{
                            $this->flashmessenger()->addMessage(array('error' => 'Imagem não enviada.'));
                        }
                    }
                }
                $this->getProdutoTable()->saveProduto($produto);
                $this->flashmessenger()->addMessage(array('success' => 'Produto modificado com sucesso.'));
                $this->redirect()->toUrl("../index");
            }
        }
        
        $view = new ViewModel(array(
            'form' => $form
        ));
        $view->setTemplate('application/produtos/form.phtml');
        return $view;
    }
    
    public function removeAction(){
    	
    	try{
    		$id = $this->params('id');
    		$produto = $this->getProdutoTable()->getProduto($id);
    		 
    		$this->getProdutoTable()->removeProduto($id);
    		 
    		$this->flashmessenger()->addMessage(array('success' => 'Produto excluído com sucesso.'));
    		$this->redirect()->toUrl("../index");
    	}catch (\Exception $e){
    		$this->flashmessenger()->addMessage(array('error' => 'O produto não existe.'));
    		 
    		$this->redirect()->toUrl("../index");
    	}
    	
    }
    
}