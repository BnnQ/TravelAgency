<div class="toast-container position-fixed" style="top: 50px; left: 50%; transform: translateX(-50%)">
    <div class="toast errorToast" role="alert" aria-live="assertive" aria-atomic="true"
         style="width: 600px!important">
        <div class="toast-header bg-danger">
            <strong class="me-auto text-light">Error</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"
                    aria-label="Close"></button>
        </div>
        <?php
        if (isset($errorMessage)) {
            echo "<div class='toast-body'><div>$errorMessage</div></div>";
        }
        ?>
    </div>
</div>
<script type="text/javascript">
    const toastElements = document.querySelector('.errorToast');
    if (toastElements) {
        const toast = new bootstrap.Toast(toastElements, {delay: 10000});
        toast.show();
    }
</script>