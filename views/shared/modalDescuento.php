<dialog
    id="miDialogoDescuento"
    class="j2-dialog j2-dialog--sm"
    aria-labelledby="j2DiscountTitle"
    aria-describedby="j2DiscountDescription"
>
    <form id="formDescuento" class="j2-dialog__layout">

        <!-- Header -->
        <header class="j2-dialog__header">

            <div class="j2-dialog__heading">

                <h2 id="j2DiscountTitle"
                    class="j2-dialog__title">
                    Aplicar descuento
                </h2>

                <p id="j2DiscountDescription"
                   class="j2-dialog__description">
                    Verifique los datos antes de continuar.
                </p>

            </div>

        </header>

        <!-- Body -->
        <div class="j2-dialog__body">

            <div class="flex flex-col gap-j2-4">

                <!-- Context Card -->
                <div class="j2-context-card">

                    <span class="j2-icon-box"
                          data-tone="warning"
                          aria-hidden="true">

                        <i class="fa-solid fa-percent"></i>

                    </span>

                    <div class="j2-context-card__content">

                        <p id="descuentoLineaDetalle"
                           class="j2-context-card__subject">
                            -
                        </p>

                        <span class="j2-badge"
                              data-tone="warning">
                            Descuento
                        </span>

                    </div>

                </div>

                <!-- Discount type -->
                <div class="j2-field">

                    <span id="j2DiscountType"
                          class="j2-field__label">
                        Tipo de descuento
                    </span>

                    <div class="j2-segmented"
                         role="radiogroup"
                         aria-labelledby="j2DiscountType">

                        <label class="j2-segmented__option">

                            <input
                                type="radio"
                                name="tipodescuento"
                                value="valor"
                                class="j2-segmented__input"
                                checked
                            >

                            <span class="j2-segmented__text">
                                Valor
                            </span>

                        </label>

                        <label class="j2-segmented__option">

                            <input
                                type="radio"
                                name="tipodescuento"
                                value="porcentaje"
                                class="j2-segmented__input"
                            >

                            <span class="j2-segmented__text">
                                Porcentaje
                            </span>

                        </label>

                    </div>

                </div>

                <!-- Discount value -->
                <div class="j2-field">

                    <label for="inputDescuento"
                           class="j2-field__label">
                        Descuento
                    </label>

                    <input
                        id="inputDescuento"
                        type="number"
                        min="0"
                        step="0.01"
                        name="descuento"
                        data-descuento=""
                        class="miles j2-input"
                        required
                    >

                </div>

                <!-- Authorization -->
                <div class="j2-field">

                    <label for="inputDescuentoClave"
                           class="j2-field__label">
                        Clave de autorización
                    </label>

                    <div class="flex flex-col">

                        <div class="j2-field__control">

                            <i class="fa-solid fa-lock j2-field__icon"
                               aria-hidden="true"></i>

                            <input
                                id="inputDescuentoClave"
                                type="password"
                                name="descuentoclave"
                                class="miles j2-input"
                                placeholder="Ingrese clave de autorización"
                                aria-describedby="divmsjalertaClaveDcto"
                            >

                        </div>

                        <div id="divmsjalertaClaveDcto"
                             class="j2-field__error [&:not(:empty)]:mt-j2-2"
                             aria-live="polite"></div>

                    </div>

                </div>

            </div>

        </div>

        <!-- Footer -->
        <footer class="j2-dialog__footer
                       flex flex-wrap items-center
                       justify-end gap-j2-3">

            <button
                type="button"
                class="salir j2-btn j2-btn--secondary
                       j2-btn--lg j2-btn--rounded-xl"
            >
                Cancelar
            </button>

            <button
                id="btnAplicarDcto"
                type="submit"
                class="aplicarDcto j2-btn
                       j2-btn--lg j2-btn--rounded-xl"
            >
                Aplicar
            </button>

        </footer>

    </form>
</dialog>