document.addEventListener("DOMContentLoaded", function () {
    document.getElementById('calcular-frete').addEventListener('click', async function() {
    const cep = document.getElementById('cep').value.replace(/\D/g, '');
    const resultadoFrete = document.getElementById('resultado-frete');
    const opcoesFrete = document.getElementById('opcoes-frete');
    resultadoFrete.textContent = 'Calculando...';
    opcoesFrete.style.display = 'none';

    if (cep.length !== 8) {
        resultadoFrete.textContent = 'Digite um CEP válido!';
        return;
    }

    try {
        // Exemplo de chamada para uma API fictícia de frete
        const response = await fetch(``);
        if (!response.ok) throw new Error('Erro ao consultar frete');
        const data = await response.json();

        // Exemplo de resposta esperada:
        // data = { PAC: 20.5, SEDEX: 35.9, PREMIUM: 50.0 }
        resultadoFrete.textContent = 'Selecione o tipo de frete:';
        opcoesFrete.style.display = 'block';

        // Atualiza os valores nos labels dos radios
        opcoesFrete.querySelectorAll('label').forEach(label => {
            const tipo = label.textContent.trim();
            if (data[tipo]) {
                label.innerHTML = `<input type="radio" name="tipo-frete" value="${tipo}" required> ${tipo} (R$ ${data[tipo].toFixed(2)})`;
            }
        });

        // Salva os valores para uso posterior
        window.valoresFrete = data;

    } catch (e) {
        resultadoFrete.textContent = 'Erro ao calcular frete. Tente novamente.';
    }
});

document.getElementById('selecionar-frete').addEventListener('click', function() {
    const tipo = document.querySelector('input[name="tipo-frete"]:checked');
    const valorFreteSelecionado = document.getElementById('valor-frete-selecionado');
    if (tipo && window.valoresFrete) {
        valorFreteSelecionado.textContent = `Frete selecionado: ${tipo.value} — R$ ${window.valoresFrete[tipo.value].toFixed(2)}`;
    } else {
        valorFreteSelecionado.textContent = 'Selecione uma opção de frete.';
    }
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
