<script type="text/javascript" id="borel">
        function montant_collecte(diviser,champ,options){
            var divisers = document.querySelector("#"+diviser),
                champs = document.querySelector("#"+champ),
                montant = document.querySelector("#montant_collecter"),
                montant2 = document.querySelector("#montant_collecter2"),
                selections = document.querySelectorAll("."+options),
                coursier = false;
            selections.forEach((selection) => {
                if(selection.checked && selection.value != 'coursier'){
                    montant.value = '';
                    montant2.value = '';
                    divisers.hidden = true;
                    champs.required = false;
                    coursier = true;
                }
            }); 
            if (!coursier) {
                divisers.hidden = false;
                champs.required = true;
            }
        }
        @if(old("mode_de_paiement") != null)
            document.querySelector('#for{{old("mode_de_paiement")}}').click();
            montant_collecte('div_collecter','montant_collecter','mode_paiement');
        @else
            document.querySelector('#forcoursier').click();
            montant_collecte('div_collecter','montant_collecter','mode_paiement');
        @endif
        function seeRecap(){
            var topass = false,
                inputs = document.querySelectorAll("input[required]"),
                selects = document.querySelectorAll("select[required]"),
                textareas = document.querySelectorAll("textarea[required]");
                inputs.forEach((input) => {
                    tableau = ['contact_colis','lieu_collecte',];
                    tableau2 = ['Produit#id','quantite#id']
                    if (document.querySelector('#type_commande').value == 'entreprise' && tableau.includes(input.id)) {
                    }else{  
                        if (input.value.trim() == '' && topass == false && !tableau2.includes(input.id)) {
                            if(input.id == 'date'){
                                input = input.nextElementSibling
                            }
                            input.focus();
                            input.click();

                            input.classList.add('is-invalid');
                            setTimeout(function(){
                                input.classList.remove('is-invalid');
                            },5000)
                            topass = true;
                        }
                    }
                });
                selects.forEach((select) => {
                    if (select.value.trim() == '' && topass == false) {
                        select.focus();
                        document.querySelector('#'+select.id+'_error').hidden = false;
                        setTimeout(function(){
                            document.querySelector('#'+select.id+'_error').hidden = true;
                        },5000)
                        topass = true;

                    }
                });
                textareas.forEach((textarea) => {
                    if (textarea.value.trim() == '' && topass == false) {
                        textarea.focus();
                        textarea.classList.add('is-invalid');
                        setTimeout(function(){
                            textarea.classList.remove('is-invalid');
                        },5000)
                        topass = true;
                    }
                });
            // modal_recap()
            if (topass == false) {
                modal_recap()
                $('#exampleModalScrollable').modal()
            }
        }
        function tosubmit(){
            document.querySelector('#description0').value = document.querySelector('#description0').value.replaceAll('\n\n','<br>')
            $('#form1').submit()
        }
        function choose(){
            var id_client = document.querySelector('#id_client2').value == '' ? 0 : document.querySelector('#id_client2').value,
                div_boutique = document.querySelector('#div_boutique'),
                id_boutique = document.querySelector('#id_boutique'),
                spinner = document.querySelector('#spinner');
            if(document.querySelector('#id_client2').value == '' ){
                spinner.hidden = true;
                id_boutique.disabled = true;
                div_boutique.hidden = false;
                id_boutique.innerHTML = "";
                document.querySelector('#erreur1').hidden = false;
                document.querySelector('#erreur1').innerHTML = "Veuillez choisir une entreprise existante";
                return  true;
            }
            id_boutique.disabled = false;
            document.querySelector('#erreur1').hidden = true;
            spinner.hidden = false;
            div_boutique.hidden = true;
            if(document.querySelector('#type_commande').value == 'entreprise'){
                jQuery.ajax({
                    url: "{{route('ClientboutiqueLie')}}",
                    type : 'POST',
                    data : { id_client : id_client , '_token' : "{{ csrf_token() }}" },
                    success: function(response)
                    {
                        spinner.hidden = true;
                        div_boutique.hidden = false;
                        id_boutique.innerHTML = response;
                    },
                    error: function(){
                        alert("Un problème est survenu veuillez atualiser la page !!!");
                    }
                });
            }
        }
        function change_value(classes){
                options = document.querySelectorAll('.'+classes);
            options.forEach((option) => {
                if (option.selected) {
                    if (classes == 'entreprise') {
                    document.querySelector('#id_client2').value = option.getAttribute("data-id")
                        document.querySelector('#contact_colis').value = option.getAttribute("data-phone")
                        phone('contact_colis')
                    }
                }
            });
        }
        document.querySelector('#Commandes')?.classList.add('active');
        function initialise(ids,faire){
            if(faire){
                classes = document.querySelectorAll('.disabled');
                classes.forEach((classe) => {
                    classe.classList.remove('disabled');
                });
            }
            ids.forEach((id) => {
                document.querySelector('#'+id).value='';
                document.querySelector('#'+id).classList.remove('disabled');
                document.querySelector('#'+id).disabled=false;
                document.querySelector('#'+id).removeAttribute("readonly");
            });
        }
        function montant_commande(){
            var depart = document.querySelector('#id_quartier_colis').value,
                resultat = document.querySelector('#montant_livraison'),
                arrivee = document.querySelector('#id_quartier_livraison').value;
            jQuery.ajax({
                    url: '{{route('Clientmontant')}}',
                    type : 'POST',
                    data : { id_depart : depart, id_arrivee : arrivee, '_token' : "{{ csrf_token() }}" },
                    success: function(response)
                    {
                        resultat.value = response;
                        remplir_montant('montant_livraison');
                        format_montant('montant_livraison');
                    },
                    error: function(){
                       resultat.value = "Un problème est survenu veuillez atualiser la page !!!";
                    }
            });
        }
        function selectionner_infos(lien,echantillon,resultat,true_value){
            var echantillon = document.querySelector('#'+echantillon),
                ville_collecte = document.querySelector('#ville_collecte'),
                ville_livraison = document.querySelector('#ville_livraison'),
                resultat = document.querySelector('#'+resultat),
                spinner = document.querySelector('#spinner2').innerHTML,
                type_commande = document.querySelector('#type_commande').value;
                if(!resultat.classList.contains("show")){
                    resultat.click();
                }
                if(true_value!=undefined){
                    var true_value = document.querySelector('#'+true_value);
                    if(true_value.value.length < 1){
                        resultat.innerHTML = '<b class="text-center">Remplir le champ pour avoir des propositions</b>';
                        return true;
                    }
                }
                resultat.innerHTML = spinner;
            jQuery.ajax({
                    url: lien,
                    type : 'POST',
                    data : { type_commande : type_commande, search : echantillon.value, ville_collecte : ville_collecte.value, ville_livraison : ville_livraison.value, '_token' : "{{ csrf_token() }}" },
                    success: function(response)
                    {
                        resultat.innerHTML = response;
                    },
                    error: function(){
                        resultat.innerHTML = " Un problème est survenu veuillez réessayer plus tard !!!";
                    }
            });
        }
        function disabled(id){
            for (var i = id.length - 1; i >= 0; i--) {
                if (document.querySelector('#'+id[i]).tagName != "INPUT") {
                    document.querySelector('#'+id[i]).classList.add("disabled");
                }else{
                    document.querySelector('#'+id[i]).setAttribute("readonly", true);
                }
            }
            
        }
        function put_information(id,info){
            for (var i = id.length - 1; i >= 0; i--) {
                document.querySelector('#'+id[i]).value = info[i];
            }
            
        }
        function change_type(valeur,passer){
            var boutique_info = document.querySelector('#boutique_info'),
                boutiques_info = document.querySelectorAll('.boutique_info'),
                collecte = document.querySelector('#collecte'),
                livraison = document.querySelector('#livraison'),
                textarea = document.querySelector('#description0'),
                simple_info = document.querySelectorAll('.simple_info');
                    document.querySelector('#type_commande').value = valeur;
            if(valeur == 'simple'){
                textarea.placeholder = 'Décrire le colis';
                collecte.hidden = false;
                livraison.classList.add("col-md-6");
                livraison.classList.remove("col-md-10");
                boutique_info.hidden = true;
                boutiques_info.forEach((info) => {
                    info.disabled=true;
                    info.required=false;
                    info.hidden = true;
                });
                simple_info.forEach((simples) => {
                    simples.disabled=false;
                    simples.required=true;
                    simples.hidden = false;
                });
            }else{
                textarea.placeholder = 'Décrire le produit';
                collecte.hidden = true;
                livraison.classList.remove("col-md-6");
                livraison.classList.add("col-md-10");
                boutique_info.hidden = false;
                boutiques_info.forEach((boutique) => {
                    boutique.disabled=false;
                    boutique.required=true;
                    boutique.hidden = false;
                });
                simple_info.forEach((simpleinfo) => {
                    simpleinfo.disabled=true;
                    simpleinfo.required=false;
                    simpleinfo.hidden = true;
                });
            }
        }
        function modal_recap(){
            var type_commande_modal = document.querySelector('#Type_commande_modal'),
            nom_client_modal = document.querySelector('#nom_client_modal'),
            telephone_client_modal = document.querySelector('#telephone_client_modal'),
            contact_colis_modal = document.querySelector('#contact_colis_modal'),
            lieu_colis_modal = document.querySelector('#lieu_colis_modal'),
            contact_livraison_modal = document.querySelector('#contact_livraison_modal'),
            lieu_livraison_modal = document.querySelector('#lieu_livraison_modal'),
            mode_paiement_modal = document.querySelector('#mode_paiement_modal'),
            description_modal = document.querySelector('#description_modal'),
            boutique_modal = document.querySelector('#boutique_modal'),
            date_modal = document.querySelector('#date_modal'),
            nom_entreprise_modal = document.querySelector('#nom_entreprise_modal');

            var type_commande = document.querySelector('#type_commande'),
                nom_client = document.querySelector('#nom_client'),
                telephone_client = document.querySelector('#phone_number'),
                contact_colis = document.querySelector('#contact_colis'),
                lieu_collecte = document.querySelector('#lieu_collecte'),
                contact_livraison = document.querySelector('#contact_livraison'),
                lieu_livraison = document.querySelector('#lieu_livraison'),
            montant_collecter = document.querySelector('#montant_collecter'),
            entreprises = document.querySelectorAll('.entreprise'),
            description = document.querySelector('#description0'),
            date = document.querySelector('#date'),
            time = document.querySelector('#time'),
            boutiques = document.querySelectorAll('.boutiques');

            type_commande_modal.innerHTML = type_commande.value
            nom_client_modal.innerHTML = nom_client.value
            telephone_client_modal.innerHTML = ' +237 '+telephone_client.value
            contact_colis_modal.innerHTML = ' +237 '+contact_colis.value
            lieu_colis_modal.innerHTML = lieu_collecte.value
            contact_livraison_modal.innerHTML = ' +237 '+contact_livraison.value
            lieu_livraison_modal.innerHTML = lieu_livraison.value

            result = time.value == '' ? "" : " à "+time.value;
            mois_lettre = [
                            'Janvier',
                            'Février',
                            'Mars',
                            'Avril',
                            'Mai',
                            'Juin',
                            'Juillet',
                            'Août',
                            'Septembre',
                            'Octobre',
                            'Novembre',
                            'Décembre'
                        ]
            date = new Date(date.value);
            jour = date.getDate();
            mois = mois_lettre[date.getMonth()];
            annee = date.getFullYear();
            date = jour+' '+mois+' '+annee;
            date_modal.innerHTML = date+result
            description_modal.innerText = description.value;

            result = montant_collecter.value == '' ? " 0 " : montant_collecter.value;
            mode_paiement_modal.innerHTML =  result +' FCFA '
            boutiques.forEach((boutique0) => {
                if(boutique0.selected == true){
                    result = boutique0.innerHTML == 'Choisir un client puis une Boutique' || boutique0.innerHTML.includes('Choisir une Boutique de')? "" : boutique0.innerHTML;
                    boutique_modal.innerHTML =  result 
                }
            });
            entreprises.forEach((entreprise) => {
                if(entreprise.selected == true){
                    result = entreprise.innerHTML == 'Choisir une Entreprise' ? "" : entreprise.innerHTML;
                    nom_entreprise_modal.innerHTML =  result 
                }
            });

        }
        function remplir_montant(id){
            var montant2 = document.querySelector('#'+id+'2')
                montant = document.querySelector('#'+id).value.replaceAll(',','');
            montant2.value = montant.replaceAll(',','')
        }
        function format_montant(id) {
            number = document.querySelector('#'+id).value.replaceAll(' ','');
            numero = 0
            number= number.replaceAll(',', "")
            number=parseInt(number);
            if (isNaN(number)) {
                document.getElementById(id).value = '';
            }else{
            number =  number.toLocaleString('fr-FR', {
                    groupSize: 3,
                    useGrouping: true,
                });
            document.getElementById(id).value =  number.replaceAll(/\s/g, ",")
            }
        }
        function  adresse_colis(classes,id1,id2){
            var options = document.querySelectorAll('.'+classes),
                input1 = document.querySelector('#'+id1),
                input2 = id2!=null ? document.querySelector('#'+id2) : null;

            options.forEach((option) => {
                if(option.selected == true){
                    input1.value = option.dataset.phone;
                    if(id2!=null){
                        input2.value = option.dataset.lieu;
                    }
                }
            });
        }

        function put_info_entreprise(){
            var boutiques = document.querySelectorAll('.boutiques'), 
                lieu_collecte = document.querySelector('#lieu_collecte'),
                id_quartier_colis = document.querySelector('#id_quartier_colis'),
                description_collecte = document.querySelector('#description_collecte');
            boutiques.forEach((option) => {
                if(option.selected == true){
                    lieu_collecte.value = option.dataset.lieu;
                    id_quartier_colis.value = option.dataset.id;
                    description_collecte.value = option.dataset.description;
                }
            });
        }
        function reinitialisation(){
            var inputs_errors = document.querySelectorAll('.is-invalid'),
                messages_errors = document.querySelectorAll('.alert'),
                messages_errors = document.querySelectorAll('.alert'),
                inputs = document.querySelectorAll('input'),
                options_ = document.querySelectorAll('option'),
                inputs_disabled = document.querySelectorAll('.disabled'),
                resultats = document.querySelectorAll('.resultat');
            inputs_errors.forEach((input) => {
                input.classList.remove('is-invalid');
            });
            messages_errors.forEach((message) => {
                message.hidden = true;
            });
            inputs_disabled.forEach((input_disabled) => {
                input_disabled.classList.remove('disabled')
            });
            inputs.forEach((change) => {
                if(change.type == 'hidden' || change.type == 'radio'){
                }else{
                    change.value = '';
                    change.disabled = false;
                    change.removeAttribute("readonly");
                }
            });
            document.querySelector('#montant_livraison').value='1000';
            remplir_montant('montant_livraison');
            format_montant('montant_livraison');
        }
    window.onload = function() {
        phone('phone_number');
        phone('contact_colis');
        phone('contact_livraison');
        remplir_montant('montant_livraison');
        format_montant('montant_livraison');
        remplir_montant('montant_collecter');
        format_montant('montant_collecter');
    };
    </script>