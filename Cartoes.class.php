<?php 

class Cartoes extends Conexao{
     private  $card_numero ,
            $card_bandeira ,
            $card_nome ,
            $mes,
            $ano,
            $card_cvv,
            $client_id,
            $card_id,
            $token,
            $chave,
            $chave_publica,
            $parcelas,
            $assinatura,
            $sandbox;


    function __construct(){
        parent:: __construct();
    }



    function Preparar($card_numero, $card_bandeira, $card_nome , $mes, $ano, $card_cvv, $client_id){
        
        
        $this->setCard_numero($card_numero);
        $this->setCard_bandeira($card_bandeira);
        $this->setCard_nome($card_nome);
        $this->setCard_mes($mes);
        $this->setCard_ano($ano);
        $this->setCard_cvv($card_cvv);
        $this->setCard_id($client_id);      
        
        
    }

        function Preparar2($card_numero, $card_bandeira, $card_nome , $mes, $ano, $card_cvv, $card_id){
        
        
        $this->setCard_numero($card_numero);
        $this->setCard_bandeira($card_bandeira);
        $this->setCard_nome($card_nome);
        $this->setCard_mes($mes);
        $this->setCard_ano($ano);
        $this->setCard_cvv($card_cvv);
        $this->setCard_id2($card_id);      
        
        
    }

function PrepararPagamento($token, $sandbox, $chave, $parcelas, $assinatura, $chave_publica){
        
        
        $this->setCard_token($token);
        $this->setCard_sandbox($sandbox);
        $this->setCard_chave($chave);
        $this->setCard_parcelas($parcelas);
        $this->setCard_assinatura($assinatura);      
        $this->setCard_chave_publica($chave_publica);      
        
    }



    function Inserir(){
/*      if($this->GetClienteCard($this->getCard_numero()) > 0){
            echo '<div class="alert alert-danger " id="erro_mostrar"> Este CPF já existe';
            Sistema::VoltarPagina();
            echo '</div>';
            exit();
        }*/
        //INSERIR OS DADOS

        //query para inserir clientes

  $query = " INSERT INTO {$this->prefix}card (card_numero, card_bandeira, card_nome, mes, ano, card_cvv,client_id)";  
        $query .=" VALUES ";
        $query .=" (:card_numero, :card_bandeira, :card_nome,:mes, :ano, :card_cvv,:client_id)"; 
   
        $params = array(
        ':card_numero'     => $this->getCard_numero() ,    
        ':card_bandeira'     => $this->getCard_bandeira() ,
        ':card_nome'=> $this->getCard_nome() ,   
        ':mes'=> $this->getCard_mes() ,
        ':ano'=> $this->getCard_ano() ,   
        ':card_cvv'    => $this->getCard_cvv() ,
        ':client_id'    => $this->getCard_id()  ,   
            
        );
            
         
          if ($this->ExecuteSQL($query, $params)) {
              header("Location:http://localhost/loja/card_registros");
          }
        

    }


 function InserirCheckout(){
/*        if($this->GetClienteCard($this->getCard_numero()) > 0){
            echo '<div class="alert alert-danger " id="erro_mostrar"> Este CPF já existe';
            Sistema::VoltarPagina();
            echo '</div>';
            exit();
        }*/
        //INSERIR OS DADOS

        //query para inserir clientes

        $query = " INSERT INTO {$this->prefix}card (card_numero, card_bandeira, card_nome, mes, ano, card_cvv,client_id)";  
        $query .=" VALUES ";
        $query .=" (:card_numero, :card_bandeira, :card_nome,:mes, :ano, :card_cvv,:client_id)"; 
   
        $params = array(
        ':card_numero'     => $this->getCard_numero() ,
        ':card_bandeira'     => $this->getCard_bandeira() ,     
        ':card_nome'=> $this->getCard_nome() ,   
        ':mes'=> $this->getCard_mes() , 
        ':ano'=> $this->getCard_ano() ,    
        ':card_cvv'    => $this->getCard_cvv() ,
        ':client_id'    => $this->getCard_id()  ,   
            
        );
            
         
          if ($this->ExecuteSQL($query, $params)) {
            //echo "<script>alert('INSERT OK')</script>";
            return true;
          }
          else{
              $teste = $this->getCard_bandeira();
             // echo "<script>alert('INSERT N OK! -> ".$teste."')</script>";
              return false;
          }
        

    }








    function GetCard($id){
        //query para buscar os produtos de uma categoria especifica.
        $query = "SELECT * FROM {$this->prefix}card WHERE client_id=$id";    
        $query .= " ORDER BY cardID DESC";

        $this->ExecuteSQL($query);

        $this->GetList();
        
    }


    function GetCardEdit($id){
        //query para buscar os produtos de uma categoria especifica.
        $query = "SELECT * FROM {$this->prefix}card WHERE cardID=$id";    
        $query .= " ORDER BY cardID DESC";

        $this->ExecuteSQL($query);

        $this->GetList2();
        
    }

   private function GetList2(){
        $i = 1;
        while($list = $this->ListarDados()):
        $this->itens[$i] = array(
             'cardID' => $list['cardID'],
             'card_bandeira'  => $list['card_bandeira'] ,
             'card_numero'  => $list['card_numero'] ,  
             'card_nome'  => $list['card_nome'] ,  
             'mes'  => $list['mes'] ,
             'ano'  => $list['ano'] ,
             'card_cvv'  => $list['card_cvv']
            );

        $i++;
        endwhile;
    }






    private function GetList(){
        $i = 1;
        while($list = $this->ListarDados()):
        $this->itens[$i] = array(
             'cardID' => $list['cardID'],
             'card_bandeira'  => $list['card_bandeira'] ,
             'card_numero'  => $list['card_numero'] ,  
             'card_nome'  => $list['card_nome'] ,  
             'mes'  => $list['mes'] ,
             'ano'  => $list['ano'] ,
             'card_cvv'  => $list['card_cvv']
            );

        $i++;
        endwhile;
    }

    function GetCardWC(){
        //query para buscar os produtos de uma categoria especifica.
        $query = "SELECT * FROM {$this->prefix}pagamento ";    
        $query .= " ORDER BY id DESC";

        $this->ExecuteSQL($query);

        $this->GetListWC();
        
    }



    private function GetListWC(){
        $i = 1;
        while($list = $this->ListarDados()):
        $this->itens[$i] = array(
             'parcelas' => $list['parcelas'],
             'sandbox' => $list['sandbox'],
             'token' => $list['token'],
              'chave' => $list['chave'],
             'assinatura' => $list['assinatura'],
             'chave_publica' => $list['chave_publica'],
           
            );

        $i++;
        endwhile;
    }




   function Delete($id){
        //query para buscar os produtos de uma categoria especifica.
        $query = "DELETE FROM {$this->prefix}card WHERE cardID=$id" ;    
    

       if ( $this->ExecuteSQL($query)) {
          header("Location:http://localhost/loja/card_registros");
       }

      
        
    }




    function EditarPagamento(){
        
        
        $query = " UPDATE {$this->prefix}pagamento SET sandbox=:sandbox,token=:token, parcelas=:parcelas, chave=:chave, chave_publica=:chave_publica, assinatura=:assinatura ";   
        $query .=" WHERE id=1"; 
   
        $params = array(
        ':sandbox' => $this->getCard_sandbox() ,    
        ':chave'=> $this->getCard_chave() ,   
        ':chave_publica'=> $this->getCard_chave_publica() ,   
        ':parcelas' => $this->getCard_parcelas() ,    
        ':assinatura' => $this->getCard_assinatura() ,
        ':token' => $this->getCard_token(),    
            
        );
                
                   
            if($this->ExecuteSQL($query, $params)):
                
                 echo "<script>
                    alert('Atualização concluida com sucesso!');
                    window.location.href= 'http://localhost//loja/adm/adm_pagamento';
                 </script>";
                
            else:
                
                    return false;
            endif;

        
    }

    //MÉTODO EDITAR
    function Editar($id){
        
              
  /*
          // verifico se ja tem este CPF no banco
        if($this->GetClienteCard($this->getCard_numero()) > 0 && $this->getCard_numero() != $_SESSION['CARD']['card_numero']):
                echo '<div class="alert alert-danger " id="erro_mostrar"> Este CPF já esta cadastrado ';
                Sistema::VoltarPagina();
                echo '</div>';
                exit();
        endif;
        */
        
        // caso passou na verificação grava no banco
        
        $query = " UPDATE {$this->prefix}card SET card_numero=:card_numero, card_bandeira=:card_bandeira, card_nome=:card_nome,mes=:mes, ano=:ano, card_cvv=:card_cvv ";   
        $query .=" WHERE cardID=:cardID"; 
   
        $params = array(
        ':card_numero'     => $this->getCard_numero() ,  
        ':card_bandeira'     => $this->getCard_bandeira() ,  
        ':card_nome'=> $this->getCard_nome() ,   
        ':mes'=> $this->getCard_mes() ,
        ':ano'=> $this->getCard_ano() ,   
        ':card_cvv'       => $this->getCard_cvv() ,    
        ':cardID'       => $this->getCard_id2() 
            
        );
        
      //  echo $query;
        
                   
            if($this->ExecuteSQL($query, $params)):
                
                 echo "<script>
                    alert('Atualização concluida com sucesso!');
                    window.location.href= 'http://localhost/loja/card_registros';
                 </script>";
                
            else:
                
                    return false;
            endif;
 
        
    }



    //BUSCAR SE O CPF DO CLIENTE JÁ EXISTE
    function GetClienteCard($card_num){
        $query = "SELECT * FROM {$this->prefix}card ";
        $query .= " WHERE card_numero = :card_numero ";
        $params = array(':card_numero'=> $card_num);
        $this->ExecuteSQL($query, $params);
        return $this->TotalDados();
    }






    // GETTERS retornando os dados do cliente 
    
    
    function getCard_numero() {
        return $this->card_numero;
    }
    
    function getCard_bandeira() {
        return $this->card_bandeira;
    }   

    function getCard_nome() {
        return $this->card_nome;
    }

    function getCard_mes() {
        return $this->mes;
    }

    function getCard_ano() {
        return $this->ano;
    }

    function getCard_cvv() {
        return $this->card_cvv;
    }
      function getCard_id() {
        return $this->client_id;

}

      function getCard_id2() {
        return $this->card_id;

}

      function getCard_token() {
        return $this->token;

}

      function getCard_assinatura() {
        return $this->assinatura;

}

      function getCard_chave() {
        return $this->chave;

}
      function getCard_parcelas() {
        return $this->parcelas;

}

      function getCard_chave_publica() {
        return $this->chave_publica;

}

      function getCard_sandbox() {
        return $this->sandbox;

}





    //  SETTERS do cliente 
    
    function setCard_numero($card_numero) {
       
        if(strlen($card_numero) < 3):
            
            
              echo '<div class="alert alert-danger " id="erro_mostrar"> Digite seu nome ';
                Sistema::VoltarPagina();
                echo '</div>';
           
            
            else:
            
            $this->card_numero = $card_numero;   
        endif;
        
     
 
    }

    function setCard_nome($card_nome) {
        
        if(strlen($card_nome) < 3):
             echo '<div class="alert alert-danger " id="erro_mostrar"> Digite seu sobrenome ';
                Sistema::VoltarPagina();
                echo '</div>';
            
            
            else:
             $this->card_nome = $card_nome;
            
        endif;
        
       
    }
    
    function setCard_bandeira($card_bandeira) {
        
        
        $this->card_bandeira = $card_bandeira;
    }
    

    function setCard_mes($mes) {
        $this->mes = $mes;
    }

    function setCard_ano($ano) {
        $this->ano = $ano;
    }

    function setCard_cvv($card_cvv) {
        $this->card_cvv = $card_cvv;
    }

    function setCard_id($client_id) {
        $this->client_id = $client_id;
    }



    function setCard_id2($card_id) {
        $this->card_id = $card_id;
    }


    function setCard_sandbox($sandbox) {
        $this->sandbox = $sandbox;
    }
    
    function setCard_token($token) {
        $this->token = $token;
    }

    function setCard_chave($chave) {
        $this->chave = $chave;
    }
    
    function setCard_chave_publica($chave_publica) {
        $this->chave_publica = $chave_publica;
    }
    
    function setCard_assinatura($assinatura) {
        $this->assinatura = $assinatura;
    }
    
    function setCard_parcelas($parcelas) {
        $this->parcelas = $parcelas;
    }

}

 ?>