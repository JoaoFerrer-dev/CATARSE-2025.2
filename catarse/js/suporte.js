// Sistema de Suporte
document.addEventListener('DOMContentLoaded', function() {
    const formSuporte = document.getElementById('form-suporte');
    const mensagemConfirmacao = document.getElementById('mensagem-confirmacao');
    const numeroProtocolo = document.getElementById('numero-protocolo');
    const btnNovaSolicitacao = document.getElementById('btn-nova-solicitacao');
    const fotosInput = document.getElementById('fotos');
    const previewFotos = document.getElementById('preview-fotos');

    // Verificar se usuário está logado
    function verificarLogin() {
        return localStorage.getItem('isLoggedIn') === 'true';
    }

    // Redirecionar para login se não estiver logado
    function redirecionarParaLogin() {
        setTimeout(() => {
            // redireciona para a página de login relativa à pasta 'paginas'
            window.location.href = 'login.html';
        }, 1000);
    }

    // Preview das fotos
    if (fotosInput) {
        fotosInput.addEventListener('change', function(e) {
            previewFotos.innerHTML = '';
            const files = Array.from(e.target.files).slice(0, 3); // Limita a 3 fotos
            
            files.forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const imgContainer = document.createElement('div');
                        imgContainer.className = 'preview-foto-item';
                        imgContainer.innerHTML = `
                            <img src="${e.target.result}" alt="Preview">
                            <button type="button" class="remover-foto">×</button>
                        `;
                        previewFotos.appendChild(imgContainer);

                        // Remover foto individual
                        imgContainer.querySelector('.remover-foto').addEventListener('click', function() {
                            imgContainer.remove();
                            // Atualizar o input de arquivo
                            const dt = new DataTransfer();
                            const remainingFiles = Array.from(fotosInput.files).filter(f => f !== file);
                            remainingFiles.forEach(f => dt.items.add(f));
                            fotosInput.files = dt.files;
                        });
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    }

    // Enviar formulário de suporte
    if (formSuporte) {
        formSuporte.addEventListener('submit', function(e) {
            e.preventDefault();

            // VERIFICAR SE USUÁRIO ESTÁ LOGADO
            if (!verificarLogin()) {
                redirecionarParaLogin();
                return;
            }

            // Validar formulário
            const tipoSolicitacao = document.getElementById('tipo-solicitacao').value;
            const descricao = document.getElementById('descricao').value.trim();

            if (!tipoSolicitacao) {
                alert('Por favor, selecione o tipo de solicitação.');
                return;
            }

            if (!descricao) {
                alert('Por favor, descreva o problema ou sua solicitação.');
                return;
            }

            // Gerar número de protocolo
            const protocolo = 'CAT' + new Date().getTime();

            // Criar objeto da solicitação
            const solicitacao = {
                protocolo: protocolo,
                tipo: tipoSolicitacao,
                numeroPedido: document.getElementById('numero-pedido').value,
                produto: document.getElementById('produto').value,
                descricao: descricao,
                data: new Date().toLocaleString('pt-BR'),
                usuario: localStorage.getItem('user_login'), // Adiciona o usuário logado
                fotos: []
            };

            // Processar fotos se existirem
            const files = Array.from(fotosInput.files);
            if (files.length > 0) {
                const processarFotos = files.map(file => {
                    return new Promise((resolve) => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            resolve(e.target.result);
                        };
                        reader.readAsDataURL(file);
                    });
                });

                Promise.all(processarFotos).then(fotosBase64 => {
                    solicitacao.fotos = fotosBase64;
                    finalizarEnvio(solicitacao);
                });
            } else {
                finalizarEnvio(solicitacao);
            }
        });
    }

    function finalizarEnvio(solicitacao) {
        // Salvar no localStorage (simulando envio para o servidor)
        const solicitacoes = JSON.parse(localStorage.getItem('solicitacoes_suporte')) || [];
        solicitacoes.unshift(solicitacao);
        localStorage.setItem('solicitacoes_suporte', JSON.stringify(solicitacoes));

        // Mostrar mensagem de confirmação
        numeroProtocolo.textContent = solicitacao.protocolo;
        formSuporte.style.display = 'none';
        mensagemConfirmacao.style.display = 'block';

        // Rolar para a mensagem de confirmação
        mensagemConfirmacao.scrollIntoView({ behavior: 'smooth' });

        // Mostrar toast de confirmação
        showToast('Solicitação enviada com sucesso!', 'success');
    }

    // Botão para nova solicitação
    if (btnNovaSolicitacao) {
        btnNovaSolicitacao.addEventListener('click', function() {
            formSuporte.reset();
            previewFotos.innerHTML = '';
            formSuporte.style.display = 'block';
            mensagemConfirmacao.style.display = 'none';
            
            // Rolar para o topo do formulário
            formSuporte.scrollIntoView({ behavior: 'smooth' });
        });
    }

    // Mostrar informações do usuário logado no formulário (opcional)
    function mostrarInfoUsuario() {
        if (verificarLogin()) {
            const usuario = localStorage.getItem('user_login');
            console.log(`Usuário logado: ${usuario} - Pode enviar solicitações`);
        } else {
            console.log('Usuário não logado - Será redirecionado ao tentar enviar');
        }
    }

    // Chamar função ao carregar a página
    mostrarInfoUsuario();
});