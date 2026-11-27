<dialog id="miDialogoDescuento" class="j2-dialog j2-dialog--sm" aria-labelledby="j2DiscountTitle">
    <form id="formDescuento" class="j2-dialog__layout">
        <header class="j2-dialog__header">
            
            <div class="j2-dialog__heading">
                <h2 id="j2DiscountTitle" class="j2-dialog__title">Aplicar descuento</h2>
                <p class="j2-dialog__description">Verifique los datos antes de continuar.</p>
            </div>
        </header>
        <div class="j2-dialog__body">
            <div class="space-y-6">
                <div class="j2-dialog__context" data-tone="primary">
                    <span class="j2-dialog__icon" aria-hidden="true">
                        <i class="fa-solid fa-gift"></i>
                    </span>
                    <div class="j2-dialog__content">
                        <p id="descuentoLineaDetalle" class="j2-dialog__subject">-</p>
                        <span class="j2-dialog__badge">Descuento</span>
                    </div>
                </div>
                <div class="j2-field">
                    <h5 class="j2-field__label" id="j2DiscountType">Tipo de descuento</h5>
                    <div class="j2-segmented" role="group" aria-labelledby="j2DiscountType">
                        <label class="j2-segmented__option">
                            <input type="radio" name="tipodescuento" value="valor" class="j2-segmented__input" checked />
                            <span class="j2-segmented__text">
                                Valor
                            </span>
                        </label>

                        <label class="j2-segmented__option">
                            <input type="radio" name="tipodescuento" value="porcentaje" class="j2-segmented__input" />
                            <span class="j2-segmented__text">
                                Porcentaje
                            </span>
                        </label>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="j2-field">
                        <label for="inputDescuento" class="j2-field__label">Descuento</label>
                        <input
                        id="inputDescuento"
                        type="number"
                        min="0"
                        step="0.01"
                        name="descuento"
                        data-descuento=""
                        class="miles j2-input"
                        required>
                    </div>

                    <div class="j2-field">
                        <label for="inputDescuentoClave" class="j2-field__label">Clave de autorizaci&oacute;n</label>
                        <div class="j2-field__control">
                            <i class="fa-solid fa-lock j2-field__icon" aria-hidden="true"></i>
                            <input
                                id="inputDescuentoClave"
                                type="password"
                                name="descuentoclave"
                                class="miles j2-input"
                                placeholder="Ingrese clave de autorizacion"
                            >
                        </div>
                        <div id="divmsjalertaClaveDcto"></div>
                    </div>
                </div>
            </div>

            </div>
<footer class="j2-dialog__footer">
<button type="button" class="salir j2-btn j2-btn--secondary j2-btn--lg j2-btn--rounded-xl">Cancelar</button>
<button id="btnAplicarDcto" type="submit" class="aplicarDcto j2-btn j2-btn--primary j2-btn--lg j2-btn--rounded-xl">Aplicar</button>
</footer>
</form>
</dialog>