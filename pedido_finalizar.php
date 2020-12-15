<?php

if(!Login::Logado()){
  Login::AcessoNegado();
  Rotas::Redirecionar(0, Rotas::pag_ClienteLogin());
}else{



if(isset($_SESSION['PRO'])) {


  if(!isset($_SESSION['PED']['frete'])){
    Rotas::Redirecionar(0, Rotas::pag_Carrinho().'#dadosfrete');
    exit ('<h4 class="alert alert-danger"> Precisa selecionar o frete! </h4>');
  }

//print_r($_POST);
//echo $_POST['destinatario'];

//echo $_SESSION['ped_cod_temp'];



  $smarty = new Template();

  $carrinho = new Carrinho();
  $clientes = new Clientes();
  $enderecos = new Enderecos();
  $pedido = new Pedidos();
  $enderecos->GetAddress($_SESSION['CLI']['cli_id']);

  $clientes->GetClientesID($_SESSION['CLI']['cli_id']);

//echo $_SESSION['parcelas'];
//print_r($_SESSION);  
//echo $_SESSION['PRO']['1']['QTD'];




    $ref_cod_pedido = date('ymdHms') . $_SESSION['CLI']['cli_id'];

    if(!isset($_SESSION['PED']['pedido'])){
      $_SESSION['PED']['pedido'] = $ref_cod_pedido;
    }

    if(!isset($_SESSION['PED']['ref'])){
      $_SESSION['PED']['ref'] = $ref_cod_pedido;
    }
//print_r($clientes->GetItens());
  
$parcelas = new Carrinho();
//print_r($parcelas->GetParcela($_SESSION['PED']['total_com_frete']));
//print_r($parcelas->GetParcela($_SESSION['PED']['total_com_frete']));
  $smarty->assign('PARCELAS',$parcelas->GetParcela($_SESSION['PED']['total_com_frete']));
  $smarty->assign('PARCELASN',$parcelas->GetParcelaN());
  $smarty->assign('CLIENTE',$clientes->GetItens());
  //$smarty->assign('PARCELA',$parcelas->GetParcela($_SESSION['PED']['frete']));
  $smarty->assign('ADDRESS', $enderecos->GetItens());
  $smarty->assign('PRO', $carrinho->GetCarrinho());
  $smarty->assign('TOTAL', Sistema::MoedaBR($carrinho->GetTotal()));
  $smarty->assign('NOME_CLIENTE', $_SESSION['CLI']['cli_nome']);
  $smarty->assign('SITE_NOME', Config::SITE_NOME);
  $smarty->assign('SITE_HOME', Rotas::get_SiteHOME());
  $smarty->assign('PAG_MINHA_CONTA', Rotas::pag_CLientePedidos());  
  $smarty->assign('TEMA', Rotas::get_SiteTEMA());
  $smarty->assign('FRETE', Sistema::MoedaBR($_SESSION['PED']['frete']));
  $smarty->assign('TOTAL_FRETE', Sistema::MoedaBR($_SESSION['PED']['total_com_frete']));

  $pedido = new Pedidos();
  $cliente = $_SESSION['CLI']['cli_id'];

  
  $_SESSION['ped_cod_temp'] = mt_rand(); 
  $cod = $_SESSION['ped_cod_temp'];

  $ref = $_SESSION['PED']['ref'];
  $frete = $_SESSION['PED']['frete'];


  $cartao = new Cartoes();
  $cartao->GetCard($_SESSION['CLI']['cli_id']);

  $email = new EnviarEmail();

  $destinatarios = array(Config::SITE_EMAIL_ADM, $_SESSION['CLI']['cli_email']);
  $assunto = 'Pedido da Loja Freitas - ' . Sistema::DataAtualBR();
  $msg = $smarty->fetch('email_compra.tpl');

  $email->Enviar($assunto, $msg, $destinatarios);


//print_r( $cartao->GetItens());
//echo "<BR><BR><BR><BR>".$_SESSION['PED']['total_com_frete'];
$smarty->assign('CARTAO', $cartao->GetItens());
$smarty->display('pedido_finalizar.tpl');

//print_r($_POST);
echo "<br><br><br>";







//print_r($carrinho->GetCarrinho());
$parcelas = $carrinho->GetCarrinho();


if (isset($_POST['assinatura'])) {


$finalidade = $_POST['destinatario'];

//PEGANDO PARCELAS, ACHANDO VALORES
$parcelas = explode("/", $_POST['parcelas'][0]);
$valor = $parcelas[0] * $parcelas[1];
$valor = rtrim($valor);
$address = new Enderecos();

//TOTAL CONSIDERANDO TAXAS DAS POR PARCELAMENTO
$total = $valor;


//PEGANDO DADOS DOS CARTÕES
$cartao = new Cartoes();
$cartao->GetCardEdit($_POST['cartao']);
$cartaodados = $cartao->GetItens();
// $carddate = explode("-", $cartaodados[1]['card_valid']);

//SE O ENDEREÇO JA ERA SALVO
if (isset($_POST['endereco'])) {

  $address->GetAddress2($_POST['endereco']);
  $endereco = $address->GetItens();
    $rua = $endereco[1]['ender_nome'];
      $ender_numero = $endereco[1]['ender_num'];
      $bairro = $endereco[1]['ender_bairro'];
       $cidade = $endereco[1]['ender_cidade'];
      $uf = $endereco[1]['ender_uf'];
      $cep = $endereco[1]['ender_cep'];
      $complemento = "Nenhum";

//SE O ENDEREÇO FOI DIGITADO NA HORA
} elseif (isset($_POST['cep'])) {
    $pais = "Brasil";   
      $rua = $_POST['rua'];
      $ender_numero = $_POST['numero'];
      $bairro = $_POST['bairro'];
      $cidade = $_POST['cidade'];
      $uf= $_POST['uf'];
      $cep = $_POST['cep'];
      $complemento = $_POST['complemento'];
      $destinatario = $_POST['nome_destinatario'];
      $ender_id = $_SESSION['CLI']['cli_id'];

$address = new Enderecos();
$address->Preparar($pais, $cep, $rua, $ender_numero, $complemento, $bairro, $cidade, $uf, $destinatario,$ender_id);
$address->InserirCheckout();

}


//SE O CARTAO JA ERA SALVO
if (isset($_POST['cartao'])) {
     $mes = $cartaodados[1]['mes'];
     $ano = $cartaodados[1]['ano'];
     $cartao_numero = $cartaodados[1]['card_numero'];
     $cartao_cvv = $cartaodados[1]['card_cvv'];
     $parcelas = $parcelas[0];

//SE O CARTAO FOI DIGITADO NA HORA   
} else if (isset($_POST['cartao_numero']) OR isset($_POST['card-nb'])) {


    $mes = $_POST['cartao_mes'];
    $ano = $_POST['cartao_ano'];
    $cartao_bandeira = $_POST['card_bandeira'];
     $cartao_numero = $_POST['cartao_numero'];
     $cartao_cvv = $_POST['cartao_cvv'];

     $cartao_nome = $_POST['cartao_nome'];
     $client_id = $_SESSION['CLI']['cli_id'];
   

$cartoes = new Cartoes();
$cartoes->Preparar($cartao_numero, $cartao_bandeira, $cartao_nome, $mes, $ano, $cartao_cvv, $client_id);
$cartoes->InserirCheckout();

}

//PREPARANDO DADOS PARA ENVIAR AO WIRECARD
$dados = array (
  "cpf" => $_POST['cpf'],
      "nome" =>  $_POST['nome'],
      "email" =>  $_POST['email'],
      "ddd" => "00", //Não tem telefone?
      "telefone" =>  $_POST['telefone'], //Não tem telefone?
      "ddi" => "55",
      "nascimento" => "1985-05-05", //Não tem data de nascimento?
      //ENDEREÇO
    "endereco" =>$rua,
      "ender_numero" => $ender_numero,
      "bairro" => $bairro,
      "cidade" => $cidade,
      "uf" => $uf,
      "cep" => $cep,
      "complemento" => $complemento,
      //ITEM
      "valor" => $valor,
      "nome_produto" => "Assinatura",
      "quantidade" => "1",
      "descricao" => "ASsinatura de produto",
      //CARTAO
      "mes" => $mes,
      "ano" => $ano,
      "numero" => $cartao_numero,
      "cvv" => $cartao_cvv,
      "parcelas"=> $parcelas[0],
    
);

//CRIANDO PEDIDO
$destinatario = $_POST['nome_destinatario'];



//ENVIANDO DADOS AO WIRECARD
$response = $carrinho->ConfirmarCompra($dados);
//AGUARDANDO 10 SEGUNDOS
sleep(5);
//VERIFICANDO STATUS DA COMPRA DO CARTAO DE CRÉDITO    
$status =  $carrinho->Webhook();

//SE O PAGAMENTO FOI APROVADO
 if ($status['status'] == "PAYMENT.AUTHORIZED" ) {
          $status_atualizado = "PAGO";
          $pedidos = new Pedidos();



          //ATUALIZA O PEDIDO COMO PAGO.
 if ( $pedido->PedidoGravar($cliente, $status['id'], $ref, $frete,$destinatario,$status_atualizado, $rua, $ender_numero, $bairro, $cidade, $uf, $cep, $complemento, $total, $finalidade, $parcelas[0])){


$pedido->ItensGravar();
  echo "<script>alert('COMPRA REALIZADA COM SUCESSO, EMBAIXO TEM OS DETALHES DO PAGAMENTO. PARA VISUALIZAR MELHOR VÁ EM MINHAS ASSINATURAS!')</script>";
 $pedido->LimparSessoes();
 
 //ENVIA PARA TELA DE PEDIDOS
 //$smarty->display('clientes_pedidos.tpl');
 
  echo "<script>window.location.href = 'http://localhost//loja/clientes_pedidos'</script>";

} else {
   
}


 

//SE O PAGAMENTO FOI RECUSADO    
} else if ($status['status'] == "PAYMENT.CANCELLED") {

   $status_atualizado = "RECUSADO";
          $pedidos = new Pedidos();

 echo "<script>alert('PAGAMENTO RECUSADO, POR ALGUM MOTIVO, CONSULTE SUA OPERADORA DE CARTOES DE CREDITO')</script>";

          //ATUALIZA O PEDIDO COMO PAGO.
 if ( $pedido->PedidoGravar($cliente, $status['id'], $ref, $frete,$destinatario,$status_atualizado, $rua, $ender_numero, $bairro, $cidade, $uf, $cep, $complemento, $total, $finalidade, $parcelas[0])){


 $pedido->LimparSessoes();






} else {
        echo "<script>alert('Ocorreu um erro, não foi possivel efetuar o pagamento. VERIFIQUE SEUS DADOS ! ')</script>";
          }
}



//SE O FORMULARIO FOI ENVIADO.

} else {


//SE O FORMULARIO NAO FOI PREENCHIDO
}

// VERIFICAR SE OS DADOS ESTÃO SENDO ENVIADOS

echo "<h1>DADOS ENVIADOS PRO PAGAMENTO</h1><br>";
print_r($dados);

echo "<h1>RESPOSTA SERVIDOR WIRECARD</h1><br>";
print_r($response);

echo "<h1>STATUS DO PAGAMENTO</h1><br>";
print_r($status);


}else{
  echo '<h4 class="alert alert-danger"> Não possui produtos no carrinho! </h4>';
  Rotas::Redirecionar(3, Rotas::pag_Produtos());
}

}


/*
echo '<pre>';
var_dump($carrinho->GetCarrinho());
echo '</pre>';
*/
 ?>