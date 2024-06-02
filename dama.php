<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Tabuleiro de Damas</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1 id="logo">Jogo de Damas</h1>

    <table style="width: 100%;">
        <tr>
            <td>
                <div id="jogador1">Jogador 1</div>
            </td>
            <td>
                <div style="text-align: center;">
                    
                    <div id="tabuleiro"></div>
                    <div id="horario">
                    <?php 
                        date_default_timezone_set('America/Sao_Paulo');
                        echo date("d/m/Y H:i:s")
                    ?>
                    </div>
                </div>            
            </td>
            <td>
                <div id="jogador2">Jogador 2</div>
            </td>
            <div id="placar"></div>
            <button id="voltarInicio" onclick="voltarParaInicio()">Voltar</button>
            <button id="reiniciar" onclick="reiniciarJogo()">Reiniciar</button>
        </tr>
    </table>
</body>
<script>
class Jogo{
    constructor(jogadorPretas,jogadorVermelhas, tabuleiro, placar_div){
        this.jogadorPretas=jogadorPretas;
        this.jogadorVermelhas=jogadorVermelhas;

        this.jogadorDaRodada=null;
        this.tabuleiro = tabuleiro;
        tabuleiro.setJogo(this);
        this.placar_div = document.getElementById("placar");
        this.placar = {
            jogadorPretas: 0,
            jogadorVermelhas: 0
        }

    }
    inicializa(){
        this.tabuleiro.inicializa();
        this.tabuleiro.distribuiPecas();
        this.atualizaPlacar();
    }
    mudaJogador(){
        if(this.jogadorDaRodada) {
            this.jogadorDaRodada.div.classList.remove("jogadorSelecionado");
        }
        if(this.jogadorPretas===this.jogadorDaRodada){
            this.jogadorDaRodada=this.jogadorVermelhas;
        }else if(this.jogadorVermelhas===this.jogadorDaRodada){
            this.jogadorDaRodada = this.jogadorPretas;
        }else{
            this.jogadorDaRodada = this.jogadorPretas;
        }
        this.jogadorDaRodada.div.classList.add("jogadorSelecionado");
    }
    atualizaPlacar() {
        this.placar_div.innerText = `Brancas: ${this.placar.jogadorPretas} / Vermelhas: ${this.placar.jogadorVermelhas}`;
    }

    aumentaPlacar(jogador) {
        if(jogador === this.jogadorPretas) {
            this.placar.jogadorPretas++
        } else if(jogador === this.jogadorVermelhas) {
            this.placar.jogadorVermelhas++
        }
        this.atualizaPlacar();
        
    }
}
class Jogador{
    constructor(id, nome, cor){
        this.div = document.getElementById(id);
        this.cor = cor;
        this.nome = nome;
        this.pecas = new Array();
        this.pecaSelecionada=null;
    }
    addPeca(peca){
        this.pecas.push(peca);
    }
}
class TipoMovimento {
    static SIMPLES = 1;
    static CAPTURA = 2;
    static DIAGONAL = 3;
}
class Peca{
    constructor(casa, classe, jogador) {
        this.jogadorDonoDaPeca = jogador;
        this.casa = casa;
        this.casa.peca=this;
        this.span = document.createElement('span');
        this.span.className=classe;
        this.tipoMovimento = TipoMovimento.SIMPLES;
        this.pecaCapturada = null;
        this.isDama = false; // Adicionando a propriedade isDama

        this.span.onclick = (event) => {
            if(this.casa.tabuleiro.jogo.jogadorDaRodada === this.jogadorDonoDaPeca){
                this.casa.tabuleiro.limpaSelecao();
                this.span.classList.add('selecionada');
                this.jogadorDonoDaPeca.pecaSelecionada = this;
            }else{
                alert('A peça não é sua!');
            }
            event.stopPropagation();
        }
        this.span.setAttribute("data-peca",this);
        casa.div.appendChild(this.span);
    }
}
class Casa{
    constructor(tabuleiro, linha, coluna) {
        this.tabuleiro = tabuleiro;
        this.linha = linha;
        this.coluna = coluna;
        this.tabuleiro.casas[linha][coluna]=this;
        this.peca = null;
        let div = document.createElement('div');
        this.div = div;
        this.div.onclick = () => {
            if(this.movimentoValido()){
                var pecaSelecionada = this.tabuleiro.jogo.jogadorDaRodada.pecaSelecionada;
                if(pecaSelecionada.tipoMovimento==TipoMovimento.SIMPLES){
                    this.setPeca(tabuleiro.jogo.jogadorDaRodada.pecaSelecionada);
                    this.tabuleiro.jogo.mudaJogador()
                }else if(pecaSelecionada.tipoMovimento==TipoMovimento.CAPTURA){
                    const pecaCapturada = pecaSelecionada.pecaCapturada;
                    if(pecaSelecionada.jogadorDonoDaPeca.cor === pecaCapturada.jogadorDonoDaPeca.cor) {
                        alert("Movimento invalido");
                        pecaSelecionada.pecaCapturada = null;
                        pecaCapturada.casa.peca = null;
                    } else {
                        this.setPeca(tabuleiro.jogo.jogadorDaRodada.pecaSelecionada);
                        pecaCapturada.span.remove()
                        this.tabuleiro.jogo.aumentaPlacar(tabuleiro.jogo.jogadorDaRodada);
                        pecaCapturada.casa.peca=null;
                        pecaSelecionada.pecaCapturada = null;
                    }
                }
                else if(pecaSelecionada.tipoMovimento==TipoMovimento.DIAGONAL) {
                    this.setPeca(tabuleiro.jogo.jogadorDaRodada.pecaSelecionada);
                }
                else{
                    alert("Erro: Tipo de movimento não definido");
                }
            }else{
                alert('Movimento inválido');
                return;
            }
        }
        div.setAttribute("data-casa",this);

        this.tabuleiro.div.appendChild(div);
        div.innerHTML="<span class='posicao'></span>";
        if(this.linha%2 == 0 && this.coluna%2 == 0){
            div.className="casa preta";
        }else if(this.linha%2 == 0 && this.coluna%2 != 0){
            div.className="casa branca";
        }else if(this.linha%2 != 0 && this.coluna%2 == 0){
            div.className="casa branca";
        }else if(this.linha%2 != 0 && this.coluna%2 != 0){
            div.className="casa preta";
        }
    }

    movimentoValido(){
        var pecaSelecionada = this.tabuleiro.jogo.jogadorDaRodada.pecaSelecionada;
        if(this.casaJaPossuiUmaPca()){
            return false;             
        }
        if(!pecaSelecionada){
            return false;
        }
        var casaAtual = pecaSelecionada.casa;
        if(this.casaAtualIgualCasaFutura(casaAtual)){
            return false;
        }

        if(pecaSelecionada.isDama) {
            pecaSelecionada.tipoMovimento = TipoMovimento.DIAGONAL;
        }
        
        var linhaAtual = casaAtual.linha;
        var colunaAtual = casaAtual.coluna;
        var linhaFutura = this.linha;
        var colunaFutura = this.coluna;
        var movimentoValido = false;
        var tamanhoDoPasso = 1;
        
        movimentoValido = this.passoValido(linhaAtual,linhaFutura,colunaAtual,colunaFutura,tamanhoDoPasso);
        
        
        if (pecaSelecionada.tipoMovimento === TipoMovimento.SIMPLES && pecaSelecionada.isDama == false) {

            if(pecaSelecionada.isDama == false && this.tabuleiro.jogo.jogadorDaRodada == this.tabuleiro.jogo.jogadorPretas && linhaAtual > linhaFutura) {
                alert("Movimento invalido");
                return false;
            }
            if (pecaSelecionada.isDama == false && this.tabuleiro.jogo.jogadorDaRodada == this.tabuleiro.jogo.jogadorVermelhas && linhaAtual < linhaFutura) {
                alert("Movimento invalido");
                return false;
            }
        }   
        if(!movimentoValido){
            tamanhoDoPasso = 2;//valida tentativa de captura
            if(pecaSelecionada.isDama == true) {
                if(pecaSelecionada.jogadorDonoDaPeca.cor == "vermelha") {
                    tamanhoDoPasso = linhaAtual-linhaFutura;
                } else {
                    tamanhoDoPasso = linhaAtual-linhaFutura;
                }
                
                console.log(tamanhoDoPasso)
            }
            movimentoValido = this.passoValido(linhaAtual,linhaFutura,colunaAtual,colunaFutura,tamanhoDoPasso);
            if(movimentoValido){
                
                const casa = this.selecionaCasaComPecaQueSeraCapturada(linhaAtual,linhaFutura,colunaAtual,colunaFutura);
                if(casa.vazia()){
                    movimentoValido = false;
                }else{
                    pecaSelecionada.tipoMovimento=TipoMovimento.CAPTURA;
                    pecaSelecionada.pecaCapturada = casa.peca;
                }
            }
        }else{
            pecaSelecionada.tipoMovimento=TipoMovimento.SIMPLES;
        }

        if (this.tabuleiro.jogo.jogadorDaRodada == this.tabuleiro.jogo.jogadorVermelhas && this.linha === 0) {
            console.log("Peça virou dama");

            if(this.tabuleiro.jogo.jogadorDaRodada.pecaSelecionada.pecaCapturada) {
                const pecaCapturada = pecaSelecionada.pecaCapturada;
                this.setPeca(tabuleiro.jogo.jogadorDaRodada.pecaSelecionada);
                pecaCapturada.span.remove()
                this.tabuleiro.jogo.aumentaPlacar(tabuleiro.jogo.jogadorDaRodada);
                pecaCapturada.casa.peca=null;
            }

            this.tabuleiro.jogo.jogadorDaRodada.pecaSelecionada.isDama = true;
            this.tabuleiro.jogo.jogadorDaRodada.pecaSelecionada.tipoMovimento = TipoMovimento.DIAGONAL;
        }

        if(this.tabuleiro.jogo.jogadorDaRodada == this.tabuleiro.jogo.jogadorPretas && this.linha === 7) {
            console.log("Peça virou dama")

            if(this.tabuleiro.jogo.jogadorDaRodada.pecaSelecionada.pecaCapturada) {
                const pecaCapturada = pecaSelecionada.pecaCapturada;
                this.setPeca(tabuleiro.jogo.jogadorDaRodada.pecaSelecionada);
                pecaCapturada.span.remove()
                this.tabuleiro.jogo.aumentaPlacar(tabuleiro.jogo.jogadorDaRodada);
                pecaCapturada.casa.peca=null;
            }

            this.tabuleiro.jogo.jogadorDaRodada.pecaSelecionada.isDama = true;
            this.tabuleiro.jogo.jogadorDaRodada.pecaSelecionada.tipoMovimento = TipoMovimento.DIAGONAL;
        }
        return movimentoValido;
    }

    passoValido(linhaAtual, linhaFutura, colunaAtual, colunaFutura, tamanhoDoPasso){
        if (Math.abs(linhaFutura - linhaAtual) === tamanhoDoPasso && Math.abs(colunaFutura - colunaAtual) === tamanhoDoPasso) {
            // Verifica se a casa destino está vazia
            if (this.tabuleiro.casas[linhaFutura][colunaFutura].peca &&
                this.tabuleiro.casas[linhaFutura][colunaFutura].peca.jogadorDonoDaPeca === this.tabuleiro.jogo.jogadorDaRodada) {
                return false; // Movimento inválido, a peça não pode capturar uma peça do mesmo time
            }

            if (this.vazia()) {
                return true; // Movimento válido para avanço simples
            } 
            else {
                const casaIntermediaria = this.tabuleiro.casas[(linhaAtual + linhaFutura) / 2][(colunaAtual + colunaFutura) / 2];
                
                if (!casaIntermediaria.vazia() && casaIntermediaria.peca.jogadorDonoDaPeca !== this.tabuleiro.jogo.jogadorDaRodada) {
                    return true; // Movimento válido para captura
                }
            }
        }
        return false; // Movimento inválido

    }

    selecionaCasaComPecaQueSeraCapturada(linhaAtual, linhaFutura, colunaAtual, colunaFutura){
        if (linhaAtual + 2 == linhaFutura && colunaAtual + 2 == colunaFutura) {
            return this.tabuleiro.casas[linhaAtual + 1][colunaAtual + 1];
        } else if (linhaAtual + 2 == linhaFutura && colunaAtual - 2 == colunaFutura) {
            return this.tabuleiro.casas[linhaAtual + 1][colunaAtual - 1];
        } else if (linhaAtual - 2 == linhaFutura && colunaAtual - 2 == colunaFutura) {
            return this.tabuleiro.casas[linhaAtual - 1][colunaAtual - 1];
        } else if (linhaAtual - 2 == linhaFutura && colunaAtual + 2 == colunaFutura) {
            return this.tabuleiro.casas[linhaAtual - 1][colunaAtual + 1];
        }else{
            return null;
        }
    }


    casaJaPossuiUmaPca(){
        if(this.peca){
            return true;
        }else{
            return false;
        }
    }

    vazia(){
        if(!this.peca){
            return true;
        }else{
            return false;
        }
    }

    casaAtualIgualCasaFutura(){
        if(this===tabuleiro.jogo.jogadorDaRodada.pecaSelecionada.casa)
            return true;
        else
            return false;
    }

    setPeca(peca){
        if(peca){
            peca.casa.peca=null;//Remove a referência da casa antiga
            this.peca = peca;
            peca.casa = this;//Adiciona a referência da casa nova
            this.div.appendChild(peca.span);
        }
    }

}
class Tabuleiro {
    constructor(id_div) {
        this.casas =  new Array(8).fill().map(() => new Array(8).fill(0));
        this.div = document.getElementById(id_div);
        this.pecas = new Array();
        this.jogo =null;
    }

    setJogo(jogo){
        this.jogo = jogo;
    }

    inicializa(){
        for(var linha=0;linha<8;linha++){
            for(var coluna=0;coluna<8;coluna++){
                let casa = new Casa(this,linha,coluna)
            }
        }
    }
    distribuiPecas(){
        for(var linha=0;linha<8;linha++){
            for(var coluna=0;coluna<8;coluna++){
                if(linha <=2){
                    this.distribuiPecaParaJogador(this.jogo.jogadorPretas,linha,coluna, "peca preta");
                }
                else if(linha >=5){
                    this.distribuiPecaParaJogador(this.jogo.jogadorVermelhas,linha,coluna, "peca vermelha");
                }
            }
        }
    }
    distribuiPecaParaJogador(jogador, linha, coluna, classe){
        let casa = this.casas[linha][coluna];
        if(this.casaValida(casa)){
            let p = new Peca(casa,classe,jogador);
            jogador.addPeca(p);
            this.pecas.splice(0,0,p);
        }
    }
    limpaSelecao(){
        for (let index = 0; index < this.pecas.length; index++) {
            const peca = this.pecas[index];
            if(peca.span.classList.contains('selecionada'))
                peca.span.classList.remove('selecionada')
        }
    }
    casaValida(casa){
        if(casa.div.classList.contains('preta')){
            return true;
        }else{
            return false;
        }
    }
}

let tabuleiro = new Tabuleiro("tabuleiro");
let jogadorPretas = new Jogador("jogador1", "Jogador 1","preta")
let jogadorVermelhas = new Jogador("jogador2","Jogador 2","vermelha")
let jogo = new Jogo(jogadorPretas, jogadorVermelhas, tabuleiro, placar);
jogo.mudaJogador()
jogo.inicializa();

function voltarParaInicio() {
    window.location.href = "index.php"; // Redireciona para a página do jogo
}

</script>
<script src="horario.js" type="text/javascript"></script>

</html>