<?php

namespace Application\Model;

use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;

class Produto implements InputFilterAwareInterface
{
    public $produto_id;
    public $produto_nome;
    public $produto_preco;
    public $produto_foto;
    public $produto_descricao;
    public $produto_status;

    protected $inputFilter;
    
    public function exchangeArray($data){
        $this->produto_id = (isset($data['produto_id']) ? $data['produto_id'] : null);
        $this->produto_nome = (isset($data['produto_nome']) ? $data['produto_nome'] : null);
        $this->produto_preco = (isset($data['produto_preco']) ? $data['produto_preco'] : null);
        $this->produto_foto = (isset($data['produto_foto']) ? $data['produto_foto'] : null);
        $this->produto_descricao = (isset($data['produto_descricao']) ? $data['produto_descricao'] : null);
        $this->produto_status = (isset($data['produto_status']) ? $data['produto_status'] : null);
    }
    
    public function getArrayCopy(){
        return get_object_vars($this);
    }
    
    public function setInputFilter(InputFilterInterface $inputFilter){
        throw new \Exception("Not used");        
    }
    
    public function getInputFilter(){
        if(!$this->inputFilter){
            $inputFilter = new InputFilter();
            
            $factory = new InputFactory();
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'produto_id',
                'required' => false,
                'filters' => array(
                    array('name' => 'Int')
                )
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'produto_nome',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim')
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                'isEmpty' => 'Campo obrigatório'
                            )
                        )                        
                    )
                )
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'produto_preco',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim')
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                'isEmpty' => 'Campo obrigatório'
                            )
                        )                        
                    )
                )
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'produto_foto',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim')
                )
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'produto_descricao',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim')
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                'isEmpty' => 'Campo obrigatório'
                            )
                        )                        
                    ),
                    array(
                        'name' => 'StringLength',
                        true,
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 10,
                            'max' => 500,
                            'message' => 'A descrição do produto deve conter entre 10 a 500 caracteres'
                        )
                    )
                )
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'produto_status',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim')
                )
            )));
            
            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }
}