<dialog id="miDialogoConfirmar"
        class="j2-dialog j2-dialog--confirmation"
        aria-labelledby="j2ConfirmTitle"
        aria-describedby="textConfirmacion textActionBadge textDescripcion">
    <div class="j2-dialog__layout">
        <header class="j2-dialog__header">
            <div class="j2-dialog__heading">
                <h2 id="j2ConfirmTitle" class="j2-dialog__title">Confirmar operación</h2>
                <p class="j2-dialog__description">Verifique los datos antes de continuar.</p>
            </div>
            <button type="button" class="j2-dialog__close" data-confirm-close aria-label="Cerrar confirmación" hidden>
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </header>
        <div class="j2-dialog__body">
            <div class="j2-confirmation__context" data-tone="primary">
                <span class="j2-confirmation__icon" aria-hidden="true">
                    <i class="fa-solid fa-gift"></i>
                </span>
                <div class="j2-confirmation__content">
                    <p id="textConfirmacion" class="j2-confirmation__subject">-</p>
                    <span id="textActionBadge" class="j2-confirmation__badge"></span>
                    <p id="textDescripcion" class="j2-confirmation__detail">-</p>
                </div>
            </div>
            <div class="j2-field">
                <label for="inputConfirmarClave" class="j2-field__label">Clave de autorización</label>
                <div class="j2-field__control">
                    <i class="fa-solid fa-lock j2-field__icon" aria-hidden="true"></i>
                    <input id="inputConfirmarClave" type="password" name="confirmarClave"
                           class="miles j2-input" placeholder="Ingrese clave de autorización"
                           aria-describedby="divmsjalertaConfirmarCortesia">
                </div>
                <div id="divmsjalertaConfirmarCortesia" aria-live="polite"></div>
            </div>
        </div>
        <footer class="j2-dialog__footer">
            <button type="button" class="cancelar j2-btn j2-btn--secondary j2-btn--lg j2-btn--rounded-xl">Cancelar</button>
            <button type="button" class="confirmar j2-btn j2-btn--primary j2-btn--lg j2-btn--rounded-xl">
                <i class="fa-solid fa-check" aria-hidden="true"></i> Confirmar
            </button>
        </footer>
    </div>
</dialog>
