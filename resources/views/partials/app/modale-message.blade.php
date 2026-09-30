<!-- pop-up message succes -->
@if(session()->has('message'))
<div class="modal fade modal-danger text-left" id="modals-success" tabindex="-1" role="dialog" aria-labelledby="modals-success" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div></div>
                <h3 class="modal-title text-dark" id="myModalLabel120"> Information </h3>
                <button type="button" class="close m-0" data-dismiss="modal" aria-label="Close" autofocus>
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <div class="modal-body">
            <p class="text-center">
                {!!session()->get('message')!!}
            </p>
        </div>
        <div class="modal-footer" style="justify-content: center;">
            <button type="button" class="btn btn-gradient-info btn-info round waves-effect waves-float waves-light" data-dismiss="modal">Terminer</button>
        </div>
        </div>
    </div>
</div>
<script type="text/javascript">
                    function success(){

                    button = document.getElementById('succes_button');
                    button.click();

                    }    
</script>
@endif
