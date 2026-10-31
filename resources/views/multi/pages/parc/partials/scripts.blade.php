{{-- Scripts communs aux écrans de gestion de parc (modales de saisie) --}}
<script type="text/javascript">
    /**
     * Ouvre une modale de saisie en la pré-remplissant.
     * Le bouton porte : data-modal (sélecteur), data-action (URL),
     * data-method (POST|PUT), data-titre et data-json (valeurs des champs).
     */
    function parcOuvrir(bouton) {
        var modal = document.querySelector(bouton.dataset.modal);
        if (!modal) { return; }
        var form = modal.querySelector('form');
        form.reset();
        form.action = bouton.dataset.action;
        var methode = form.querySelector('input[name="_method"]');
        if (methode) { methode.value = bouton.dataset.method || 'POST'; }
        var action = form.querySelector('input[name="_action"]');
        if (action) { action.value = bouton.dataset.action; }
        var titre = modal.querySelector('.modal-title');
        if (titre && bouton.dataset.titre) { titre.textContent = bouton.dataset.titre; }
        var donnees = bouton.dataset.json ? JSON.parse(bouton.dataset.json) : {};
        Object.keys(donnees).forEach(function (cle) {
            var champ = form.querySelector('[name="' + cle + '"]');
            if (!champ || champ.type === 'file') { return; }
            if (champ.type === 'checkbox') {
                champ.checked = donnees[cle] == 1 || donnees[cle] === true;
            } else {
                champ.value = donnees[cle] === null || donnees[cle] === undefined ? '' : donnees[cle];
            }
            if (champ.tagName === 'SELECT' && window.jQuery && jQuery(champ).hasClass('select2-hidden-accessible')) {
                jQuery(champ).trigger('change');
            }
        });
        if (typeof parcApresOuverture === 'function') { parcApresOuverture(form); }
        jQuery(modal).modal('show');
    }

    @if($errors->any() && old('_form'))
    // Une validation a échoué : on rouvre la modale concernée avec les valeurs saisies.
    document.addEventListener('DOMContentLoaded', function () {
        var modal = document.querySelector('{{ old('_form') }}');
        if (!modal) { return; }
        var form = modal.querySelector('form');
        @if(old('_action'))
        form.action = {{ json_encode(old('_action')) }};
        @endif
        if (typeof parcApresOuverture === 'function') { parcApresOuverture(form); }
        jQuery(modal).modal('show');
    });
    @endif
</script>
