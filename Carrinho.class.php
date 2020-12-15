<?php 
require 'lib/autoload.php';

use Moip\Moip;
use Moip\Resource\Payment;
use Moip\Auth\BasicAuth;
use Moip\Exceptions;


class Carrinho{
	private $total_valor, $total_peso, $itens = array();


	function GetCarrinho($sessao=NULL){

		$i = 1; $sub = 1.00; $peso = 0;

		
		foreach ($_SESSION['PRO'] as $lista) {
      
			$sub = $lista['VALOR'] * $lista['QTD'];
			$this->total_valor += $sub;

			$peso = $lista['PESO'] *  $lista['QTD'];
			$this->total_peso += $peso;

			$this->itens[$i] = array(

				'pro_id' => $lista['ID'],
				'pro_nome'  => $lista['NOME'],
	            'pro_valor' => $lista['VALOR'], // 1.000,99
	            'pro_valor_us' => $lista['VALOR_US'],  //1000.99
	            'pro_peso'  => $lista['PESO'],
	            'pro_qtd'   => $lista['QTD'],
	            'pro_img'   => $lista['IMG'],
	            'pro_link'  => $lista['LINK'],
	            'pro_subTotal'=> Sistema::MoedaBR($sub),         
	            'pro_subTotal_us'=> $sub 

				);
			$i++;

			
		}

		if(count($this->itens) > 0){
			return $this->itens;
		}else{
			echo '<h4 class="alert alert-danger"> Não há produtos no carrinho </h4>';

		}

	}


	function GetTotal(){
		return $this->total_valor;
	}

	function GetPeso(){
		return $this->total_peso;
	}

	function CarrinhoADD($id,$qtd){
		$produtos = new Produtos();
		$produtos->GetProdutosID($id);
    
		foreach ($produtos->GetItens() as $pro) {
			$ID = $pro['pro_id'];
			$NOME  = $pro['pro_nome'];
      $QTD_UM = $qtd;
          if ($qtd == "1") {
           $qtd = $pro['pro_valor'];
        } else if($qtd == "2") {
            $qtd = $pro['pro_bimestral'];
        } else if ($qtd == "3") {
             $qtd = $pro['pro_trimestral'];
        } else if ($qtd == "6") {
             $qtd = $pro['pro_semestral'];
        } else if ($qtd == "12")  {
             $qtd = $pro['pro_anual'];
        }
            $_SESSION['PARCELAS'] =  $QTD_UM;
            $VALOR_US = $pro['pro_valor_us'];
            $VALOR  = $qtd;
            $PESO  = $pro['pro_peso'];
            $QTD   = $QTD_UM;

            if ($QTD_UM > '12') {
              $QTD_UM = '12';
            }

            
            $_SESSION['parcelas']   = $QTD_UM;
            $IMG   = $pro['pro_img'];
            $LINK  = Rotas::pag_ProdutosInfo().'/'.$ID.'/'.$pro['pro_slug'];
            $ACAO  = $_POST['acao'];
		}

		switch ($ACAO) {
			case 'add':
					if(!isset($_SESSION['PRO'][$ID]['ID'])){
						$_SESSION['PRO'][$ID]['ID'] = $ID;
						$_SESSION['PRO'][$ID]['NOME']  = $NOME;
					    $_SESSION['PRO'][$ID]['VALOR'] = $VALOR;
					    $_SESSION['PRO'][$ID]['VALOR_US'] = $VALOR;
					    $_SESSION['PRO'][$ID]['PESO']  = $PESO;
					    $_SESSION['PRO'][$ID]['QTD']   = $QTD;
					    $_SESSION['PRO'][$ID]['IMG']   = $IMG;
					    $_SESSION['PRO'][$ID]['LINK']  = $LINK;  
					}else{
						 $_SESSION['PRO'][$ID]['QTD']   += $QTD;
					}

				break;

			case 'del':
				$this->CarrinhoDEL($id);
				break;

			case 'limpar':
				$this->CarrinhoLimpar();
				break;
			
			
		}
	}


	private function CarrinhoDEL($id){
		unset($_SESSION['PRO'][$id]);
	}

	private function CarrinhoLimpar(){
		unset($_SESSION['PRO']);
	}



  
function GetParcelaN() {
$parcelas = new Cartoes();
$parcelas->GetCardWC();
$parcel = $parcelas->GetItens();

if ( $_SESSION['PRO']['1']['QTD'] > '12') {
     $_SESSION['PRO']['1']['QTD'] = '12';
}

for ($i=1; $i <=$_SESSION['PRO']['1']['QTD']; $i++) { 
  $parcelasn[$i] = $i ;
  }
return $parcelasn;
//return $parcel ;
}




function GetParcela($valor) {


if ( $_SESSION['PRO']['1']['QTD'] > '12') {
     $_SESSION['PRO']['1']['QTD'] = '12';
}


$parcelas = new Cartoes();
$parcelas->GetCardWC();
$parcel = $parcelas->GetItens();
//$parcelamento = $parcel[1]['parcelas']."x - ".$valor; 
for ($i=1; $i <=$_SESSION['parcelas']; $i++) { 
switch ($i) {
 case "1": 
      $taxa = $valor* 1;
 break;
 case "2": 
      $taxa = $valor* 1.045;
 break;
  case "3": 
      $taxa = $valor* 1.050;
 break;
  case "4": 
      $taxa = $valor* 1.055;
 break;
  case "5": 
      $taxa = $valor* 1.065;
 break;
   case "6": 
      $taxa = $valor* 1.075;
 break;
   case "7": 
      $taxa = $valor* 1.085;
 break;
   case "8": 
      $taxa = $valor* 1.095;
 break;
   case "9": 
      $taxa = $valor* 1.105;
 break;
   case "10": 
      $taxa = $valor* 1.115;
 break;  
 case "11": 
      $taxa = $valor* 1.120;
 break;
  case "12": 
      $taxa = $valor* 1.125;
 break;

}

  $parcelamento[$i] = ($taxa/$i);
  $parcelamento[$i] = number_format($parcelamento[$i], 2, ',', '');
}
     
  return  $parcelamento;
 }


function ConfirmarCompra($dados) {


    $dados['descricao'] = "Assinatura de Produto";

		$dados['cliente'] = array(
      "cpf" =>$dados['cpf'],
      "nome" => $dados['nome'],
      "email" => $dados['email'],
      "ddd" => "00", //Não tem telefone?
      "telefone" => $dados['telefone'], //Não tem telefone?
      "ddi" => "55",
      "nascimento" => "1985-05-05", //Não tem data de nascimento?
      "endereco" =>$dados['endereco'],
      "numero" =>$dados['ender_numero'],
      "bairro" => $dados['bairro'],
      "cidade" =>$dados['cidade'],
      "uf" => $dados['uf'],
      "cep" => $dados['cep'],
      "complemento" => $dados['complemento'],
    );

    $dados['itens'][0] = array(
      "valor" => $dados['valor'],
      "nome" => "Assinatura de Produto",
      "quantidade" => "1",
      "descricao"=>"Compra de assinatura",
      
    );

//    $cardExpiry = explode("/", $post['cardExpiry']);
    $dados['cartao'] = array(
      "mes" => $dados['mes'],
      "ano" => $dados['ano'],
      "numero" => $dados['numero'],
      "cvv" => $dados['cvv'],
      "parcelas" => $dados['parcelas'],
    );
    


 
	$data = $this->processaPagamento($dados);



return $data;

}


function processaPagamento($dados) {
  $pedidos = new Cartoes();
$pedidos->GetCardWC();
$data =  $pedidos->GetItens();



$token = $data[1]['token'];
$key =  $data[1]['chave'];

if ($data[1]['sandbox'] == "ATIVO") {
   $endpoint = "https://sandbox.moip.com.br/";
} else {
   $endpoint = "https://api.moip.com.br/";
}

	
	 $moip = new Moip(new BasicAuth($token, $key), $endpoint);

  
        $cli = $dados['cliente'];
          try {
              $cliente = $moip->customers()->setOwnId(uniqid('CUS-'))
                  ->setFullname(@$cli['nome'])
                  ->setEmail(@$cli['email'])
                  ->setTaxDocument(@$cli['cpf'], 'CPF')
                  ->setPhone(@$cli['ddd'], @$cli['telefone'], @$cli['cod_pais'])
                  ->setBirthDate(@$cli['nascimento'])
                  ->addAddress("SHIPPING",
                      @$cli['endereco'], @$cli['numero'],
                      @$cli['bairro'], @$cli['cidade'], @$cli['uf'],
                      @$cli['cep'], @$cli['complemento'])
                  ->create();


        $cli['id'] = $cliente->getId();
        $resposta['id_cliente'] = $cli['id'];

        $item = $dados['itens'][0];

          $pedido = $moip->orders()->setOwnId(uniqid('WIRECARD-'));
              $pedido->setCustomer($cliente) ;         
              $valor = intval(str_replace('.','',$item['valor']));
              $pedido->addItem($item['nome'], (int)$item['quantidade'], $item['descricao'], $valor);
          
          $pedido->create();
      

        if (empty($pedido->getId())) {
          echo "Erro ao criar o pedido";
          return false;
        }
        $resposta['id_pedido'] = $pedido->getId();

        $cc = $dados['cartao'];
        $titular = $moip->holders()->setFullname(@$cli['nome'])
            ->setBirthDate(@$cli['nascimento'])
            ->setTaxDocument(@$cli['cpf'], 'CPF')
            ->setPhone(@$cli['ddd'], @$cli['telefone'], @$cli['cod_pais'])
            ->setAddress("SHIPPING",
                      @$cli['endereco'], @$cli['numero'],
                      @$cli['bairro'], @$cli['cidade'], @$cli['uf'],
                      @$cli['cep'], @$cli['complemento']);      

        if (!empty($cc)) {
       

        
                $pagamento = new Payment($moip);
                $pagamento->setOrder($pedido)
                    ->setCreditCard($cc['mes'], $cc['ano'], $cc['numero'], $cc['cvv'], $titular)
                    ->setInstallmentCount($cc['parcelas'])
                    ->setStatementDescriptor(substr($dados['descricao'], 0, 12))
                    ->execute();
        
        } else {
          echo "Faltam dados do cartão";
          return false;
        }

        if (empty($pagamento->getId())) {
          echo "Erro ao criar o pagamento";
          return false;
        }
$pagamento->getStatus();




     $resposta['status'] = $pagamento->getStatus();
      

        } catch (\Moip\Exceptions\UnautorizedException $e) {

       echo "<script>
                    alert('Ocorreu um erro de instabilidade durante o pagamento. Tente novamente mais tarde'.');
                    window.location.href= 'http://localhost//loja/clientes_pedidos';
                 </script>";
    echo $e->getMessage();             
} catch (\Moip\Exceptions\ValidationException $e) {
	
       echo "<script>
                    alert('Ocorreu um erro de instabilidade durante o pagamento. Tente novamente mais tarde'.');
                    window.location.href= 'http://localhost//loja/clientes_pedidos';
                 </script>";
         printf($e->__toString());         
  
} catch (\Moip\Exceptions\UnexpectedException $e) {

       echo "<script>
                    alert('Ocorreu um erro de instabilidade durante o pagamento. Tente novamente mais tarde'.');
                    window.location.href= 'http://localhost//loja/clientes_pedidos';
                 </script>";
     echo $e->getMessage();            

}


  return $resposta;
}


 function Webhook() {

$pedidos = new Cartoes();
$pedidos->GetCardWC();
$data =  $pedidos->GetItens();


$token = $data[1]['token'];
$key =  $data[1]['chave'];
$sandbox = $data[1]['sandbox'];
$crypt = "$token:$key";
$auth = base64_encode($crypt);

if ($sandbox == "ATIVO") {
  $endpoint = "https://sandbox.moip.com.br/v2/webhooks";
} else {
  $endpoint = "https://api.moip.com.br/v2/webhooks";
}

		$ch = curl_init($endpoint);
		curl_setopt_array($ch, [
   		 // Equivalente ao -X:
   		 CURLOPT_CUSTOMREQUEST => 'GET',
   		 // Equivalente ao -H:
   		 CURLOPT_HTTPHEADER => [
        'authorization: Basic '.$auth.''
   		 ],
   		 // Permite obter o resultado
   		 CURLOPT_RETURNTRANSFER => 1,
		]);

		$resposta = json_decode(curl_exec($ch), true);
		curl_close($ch);
		setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
		date_default_timezone_set('America/Sao_Paulo');
		$date = strftime('%d/%m/%Y as %H:%M:%S');

		foreach ($resposta as $a) {
		$id = $a[0]['resourceId'];
		$status = $a[0]['event'];
		$data =  $date;
	}

	$status = array (
		"id" => $id,
		"status" => $status,
		"data" => $data,
	);

	return $status;
}






}

 ?>