<?php
/* ========================================================================
 * Extensão Frete Grátis
 * http://brunobrasilweb.com.br
 * ========================================================================
 * Copyright (c) 2013 @brunobrasilweb.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * thtp://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ======================================================================== */

/**
 * Brunobrasilweb_FreteGratis_Model_Carrier_Fretegratis
 *
 * @category   Brunobrasilweb
 * @package    Brunobrasilweb_FreteGratis
 * @author     Bruno Brasil <contato@brunobrasilweb.com.br>
 */

class Brunobrasilweb_FreteGratis_Model_Carrier_Fretegratis extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {

    protected $_code = 'fretegratis';
    protected $_name = 'Gratis';
    
    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
        $cep = $request->getDestPostcode();
        $totalPedido = $request->getPackageValue();
        $result = Mage::getModel('shipping/rate_result');
        
        if ($this->getConfigData('active') && (!$this->getConfigData('valor_pedido') || $totalPedido >= $this->getConfigData('valor_pedido'))) {
            if (($this->cepNaFaixa($cep) && $this->getConfigData('frete_gratis_por_faixa')) || !$this->getConfigData('frete_gratis_por_faixa')) {
                $method = Mage::getModel('shipping/rate_result_method');
                $method->setCarrier($this->_code);
                $method->setCarrierTitle($this->getConfigData('title'));
                $method->setMethod($this->_code);
                $method->setMethodTitle($this->_name);
                $method->setPrice('0.00');
                $method->setCost('0.00');
                $result->append($method);
            }    
        }
        
        return $result;
    }

    public function getAllowedMethods() {
        return array($this->_code => $this->_name);
    }
    
    private function cepNaFaixa($cep) {
        $cep = preg_replace("/[^0-9]/", "", $cep);
        $faixasCep = explode("\n", $this->getConfigData('faixas_frete_gratis'));
        
        foreach ($faixasCep as $faixa) {
            $f = explode('|', $faixa);
            $de =  preg_replace("/[^0-9]/", "", $f[0]);
            $ate =  preg_replace("/[^0-9]/", "", $f[1]);
            
            if ($cep >= $de && $cep <= $ate)
                return true;
        }
        
        return false;
    }

}
