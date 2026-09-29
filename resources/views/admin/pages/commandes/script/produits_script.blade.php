<script type="text/javascript">
        tableau_produits = [];
        exemple_ligne = document.querySelector('#exemple_ligne_produit').innerHTML;
        function  upgrade_ligne_produit(classes){
            var options = document.querySelectorAll('.'+classes);
                ajout = type_changement(classes);
            options.forEach((option) => {
                if (ajout) {
                    if(option.selected == true){
                        if (document.querySelector('#liste_'+option.dataset.id) == null){
                            content = exemple_ligne.replaceAll('#produit',option.dataset.nom)
                            content = content.replaceAll('#id',option.dataset.id)
                            document.querySelector('#lister_produits').insertAdjacentHTML('beforeend', content)
                        }
                    }
                }else{
                    if(!option.selected){
                        if (document.querySelector('#liste_'+option.dataset.id) != null)
                        document.querySelector('#liste_'+option.dataset.id).remove();

                    }
                }

            });
            voir_description(classes,'description0')
        }
        upgrade_ligne_produit('decrire')
        function  voir_description(classes,id){
            var options = document.querySelectorAll('.'+classes),
                table_produit = document.querySelector('#table_produit'),
                textarea = document.querySelector('#'+id);
                textarea.value ='';
                row = 1;
                table_produit.value=''
            options.forEach((option) => {
                if(option.selected == true){
                    valeur_table = row == 1 ? option.dataset.id : ','+option.dataset.id;
                    table_produit.value += valeur_table;
                    textarea.value += row+') -> '+option.dataset.description+' ('+document.querySelector('#quantite'+option.dataset.id).value+')\n\n';
                    textarea.placeholder = 'Décrire le produit';
                    row++
                }else{
                    textarea.placeholder = 'Décrire le colis';
                }
            });
            textarea.style.height = row*50+'px'
        } 
        function type_changement(classes){
            var options = document.querySelectorAll('.'+classes),
            result = true;
                comparate = [];
            options.forEach((option) => {
                if(option.selected == true){
                    comparate.push(option.dataset.id)
                }

            });
            if (tableau_produits.length >= comparate.length) {
                result = false;
            }
            tableau_produits = comparate;
            return result;
        }
        function delete_ligne(chiffre_id,classes){

            var options = document.querySelectorAll('.'+classes),
                evenement = new Event('change'),
                select = document.querySelector('#produit');
            options.forEach((option) => {
                if(option.selected == true  && option.dataset.id == chiffre_id){
                    option.selected = false;
                    select.dispatchEvent(evenement)
                }

            });
            return true;
        }
</script>