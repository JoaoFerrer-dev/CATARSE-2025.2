document.addEventListener("DOMContentLoaded", function () {
    // Copia lógica de frete do produto4.js para todos os produtos
    async function calcularFreteCorreios() {
        const cepDestino = document.getElementById("cep")?.value.replace(/\D/g, '');
        if (!cepDestino || cepDestino.length !== 8) {
            alert("Digite um CEP válido!");
            return;
        }
        try {
            const response = await fetch(`https://viacep.com.br/ws/${cepDestino}/json/`);
            const dadosCep = await response.json();
            if (dadosCep.erro) {
                alert("CEP inválido! Tente novamente.");
                return;
            }
            // Simulação de frete baseado no destino
            const fretePadrao = (Math.random() * 20 + 10).toFixed(2);
            const freteExpress = (Math.random() * 40 + 20).toFixed(2);
            const fretePremium = (Math.random() * 60 + 30).toFixed(2);
            document.getElementById("resultado-frete").innerHTML = `
                <p>🚚 Frete para ${dadosCep.localidade} - ${dadosCep.uf}</p>
                <div class=\"frete-option\">📦 PAC (5-10 dias úteis): <strong>R$ ${fretePadrao}</strong></div>
                <div class=\"frete-option\">⚡ Sedex (1-3 dias úteis): <strong>R$ ${freteExpress}</strong></div>
                <div class=\"frete-option\">✈️ Premium (1 dia útil): <strong>R$ ${fretePremium}</strong></div>
            `;
            // Exibe opções de frete
            const opcoesFrete = document.getElementById('opcoes-frete');
            opcoesFrete.style.display = 'block';
            opcoesFrete.dataset.pac = fretePadrao;
            opcoesFrete.dataset.sedex = freteExpress;
            opcoesFrete.dataset.premium = fretePremium;
            opcoesFrete.dataset.cep = cepDestino;
        } catch (error) {
            alert("Erro ao calcular frete! Tente novamente mais tarde.");
        }
    }
    const btnCalcularFrete = document.getElementById("calcular-frete");
    if (btnCalcularFrete) btnCalcularFrete.addEventListener("click", calcularFreteCorreios);

    // Exibe valor do frete selecionado
    document.querySelectorAll('input[name="tipo-frete"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const opcoesFrete = document.getElementById('opcoes-frete');
            let valor = '';
            if (this.value === 'PAC') valor = opcoesFrete.dataset.pac;
            if (this.value === 'SEDEX') valor = opcoesFrete.dataset.sedex;
            if (this.value === 'PREMIUM') valor = opcoesFrete.dataset.premium;
            document.getElementById('valor-frete-selecionado').textContent = valor ? `Frete selecionado: R$ ${valor}` : '';
        });
    });

    // Botão para selecionar e salvar o frete escolhido
    const btnSelecionarFrete = document.getElementById('selecionar-frete');
    if (btnSelecionarFrete) {
        btnSelecionarFrete.addEventListener('click', function() {
            const opcoesFrete = document.getElementById('opcoes-frete');
            const tipoFrete = document.querySelector('input[name="tipo-frete"]:checked');
            if (!tipoFrete) {
                alert('Por favor, selecione o tipo de frete.');
                return;
            }
            let valorFrete = 0;
            let tipo = tipoFrete.value;
            if (tipo === 'PAC') valorFrete = opcoesFrete.dataset.pac;
            if (tipo === 'SEDEX') valorFrete = opcoesFrete.dataset.sedex;
            if (tipo === 'PREMIUM') valorFrete = opcoesFrete.dataset.premium;
            localStorage.setItem('frete', JSON.stringify({ tipo, valor: valorFrete, cep: opcoesFrete.dataset.cep }));
            document.getElementById('valor-frete-selecionado').textContent = `Frete selecionado: ${tipo} - R$ ${valorFrete}`;
            alert('Frete selecionado salvo!');
        });
    }
});
