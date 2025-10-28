document.addEventListener('DOMContentLoaded', () => {
  const popup = document.getElementById('popupConfirmacao');
  const backdrop = document.getElementById('popupBackdrop');
  const btnConfirm = document.getElementById('confirmarDelete');
  const btnCancel = document.getElementById('cancelarDelete');

  let formParaDeletar = null;

  // garantir que todos os botões de delete não submetam por padrão
  // e abram o modal ao clicar
  document.querySelectorAll('.del-ico').forEach(btn => {
    // force o tipo button caso esteja como submit
    if (btn.tagName.toLowerCase() === 'button') btn.type = 'button';

    btn.addEventListener('click', (e) => {
      formParaDeletar = btn.closest('form');
      abrirModal();
    });
  });

  function abrirModal() {
    popup.classList.add('active');
    popup.setAttribute('aria-hidden', 'false');
    // impedir scroll de fundo
    document.documentElement.style.overflow = 'hidden';
    document.body.style.overflow = 'hidden';
  }

  function fecharModal() {
    popup.classList.remove('active');
    popup.setAttribute('aria-hidden', 'true');
    document.documentElement.style.overflow = '';
    document.body.style.overflow = '';
    formParaDeletar = null;
  }

  btnConfirm.addEventListener('click', () => {
    if (formParaDeletar) {
      // enviar o form que contém o botão de excluir
      formParaDeletar.submit();
    }
  });

  btnCancel.addEventListener('click', fecharModal);

  // fechar clicando no backdrop (fora da caixa)
  backdrop.addEventListener('click', fecharModal);

  // fechar com ESC
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && popup.classList.contains('active')) {
      fecharModal();
    }
  });
});