<?php
/* Template Name: Accept payment */

if ( ! empty( $_GET['taskKey'] ) ) {
	$price = FD_Moving_Request::get_price( $_GET['taskKey'] )->get_data();

	if ( ! $price->success ) {
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}
} else {
	wp_safe_redirect( home_url( '/' ) );
	exit;
}

get_header();
?>
	<div class="page gray-background" id="accept-payment-page" style="padding-top: 160px">
        <div class="modal__overlay closed" id="modal_accept_payment">
          <div class="modal__popup" style="max-width: 569px; height: 542px; background-color: #f0f7fb">
            <button
              type="button"
              class="modal__close-button"
              id="modal_accept_payment_button_close"
              style="background-color: #f0f7fb"
            >
              <svg
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="#f0f7fb"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M18.5312 16.8633C18.918 17.293 18.918 17.9375 18.5312 18.3242C18.1016 18.7539 17.457 18.7539 17.0703 18.3242L12 13.2109L6.88672 18.3242C6.45703 18.7539 5.8125 18.7539 5.42578 18.3242C4.99609 17.9375 4.99609 17.293 5.42578 16.8633L10.5391 11.75L5.42578 6.63672C4.99609 6.20703 4.99609 5.5625 5.42578 5.17578C5.8125 4.74609 6.45703 4.74609 6.84375 5.17578L12 10.332L17.1133 5.21875C17.5 4.78906 18.1445 4.78906 18.5312 5.21875C18.9609 5.60547 18.9609 6.25 18.5312 6.67969L13.418 11.75L18.5312 16.8633Z"
                  fill="#999999"
                />
              </svg>
            </button>
            <div class="modal__content" style="height: 100%">
                <div class="accept-payment__status-container error" id="accept-payment__status-container">
                    <div class="accept-payment__loader"></div>
                    <div class="accept-payment__error">
											<p>FEL!</p>
											<button id="accept-payment__try-again-button" class="button">Försök igen</button>
										</div>
                    <div class="accept-payment__wrongSignerName"><p>FEL!</p><p><label>Namnet i formuläret är felaktigt, vänligen ange ett namn som stämmer överens med ditt BankID.</label></p></div>
                    <div class="accept-payment__duplicateSigningTaskError"><p>FEL!</p><p><label>Redan signerad.</label></p></div>
										<div class="accept-payment__success"><p class="head">Klart!</p><p class="content">Erbjudandet är godkänt och signerat med BankID.</p><button class="button" id="accept-payment__success-button">Ok</button></div>
                    <div class="accept-payment__container">
                        <h3 class="accept-payment__header">Logga in med BankID</h3>
                        <div class="accept-payment__qrcode-container">
                            <img class="accept-payment__qrcode" id="accept-payment__qrcode-img" src="" alt="">
                            <a href="" class="accept-payment__link button hidden" id="accept-payment__link">
                                Öppna BankID-appen
                            </a>
                        </div>
                        <p class="accept-payment__text">Starta BankID-appen och tryck på knappen Skanna QR. Skanna sedan QR-koden ovan.</p>
                        <button class="accept-payment__button" id="accept-payment__submit-bankid-link">Alternativ: Starta BankID på den här enheten</button>
                        <button class="accept-payment__button hidden" id="accept-payment__submit-bankid-qrcode">Få qrcode att signera</button>
                    </div>
                </div>
            </div>
          </div>
        </div>
		<div class="page__container">
			<div class="container__header">
				<h1 class="container__title">Acceptera offert</h1>
				<?php
				if ( ! empty( $price->data->movingPrice ) ) {
				?>
					<h2 class="container__id">ID <?php echo sprintf( "%06d", $price->task_id ); ?></h2>
					<?php
					}
				?>
			</div>
			<div class="container__content">
				<div class="container__row">
					<section class="container__section">
						<h3 class="section__title">Kundens pris</h3>
						<div class="section__divider"></div>
						<?php
						if ( ! empty( $price->data->movingPrice ) ) {
							?>
							<div class="section__row">
								<div class="section__key">Flyttjänst</div>
								<div class="section__value align-right"><?php echo $price->data->movingPrice; ?> SEK</div>
							</div>
							<?php
						}

						if ( ! empty( $price->data->packingPrice ) ) {
						?>
							<div class="section__row">
								<div class="section__key">Förpackning i kartonger</div>
								<div class="section__value align-right"><?php echo $price->data->packingPrice; ?> SEK</div>
							</div>
							<?php
						}

						if ( ! empty( $price->data->unpackingPrice ) ) {
						?>
							<div class="section__row">
								<div class="section__key">Uppackning av kartonger</div>
								<div class="section__value align-right"><?php echo $price->data->unpackingPrice; ?> SEK</div>
							</div>
							<?php
						}

						if ( ! empty( $price->data->assemblingPrice ) ) {
							?>
							<div class="section__row">
								<div class="section__key">Montering av stora möbler</div>
								<div class="section__value align-right"><?php echo $price->data->assemblingPrice; ?> SEK</div>
							</div>
							<?php
						}

						if ( ! empty( $price->data->disassemblingPrice ) ) {
							?>
							<div class="section__row">
								<div class="section__key">Nedmontering av stora möbler</div>
								<div class="section__value align-right"><?php echo $price->data->disassemblingPrice; ?> SEK</div>
							</div>
							<?php
						}

						if ( ! empty( $price->data->cleaningPrice ) ) {
							?>
							<div class="section__row">
								<div class="section__key">Städning</div>
								<div class="section__value align-right"><?php echo $price->data->cleaningPrice; ?> SEK</div>
							</div>
							<?php
						}

						if ( ! empty( $price->data->discount ) ) {
						?>
							<div class="section__row">
								<div class="section__key">Rabbat</div>
								<div class="section__value align-right"><?php echo $price->data->discount; ?> SEK</div>
							</div>
							<?php
						}

						if ( ! empty( $price->data->totalPrice ) ) {
						?>
							<div class="section__row">
								<div class="section__key bold">Total</div>
								<div class="section__value align-right bold"><?php echo $price->data->totalPrice; ?> SEK</div>
							</div>
							<?php
						}

						if ( ! empty( $price->data->taxDeduction ) ) {
						?>
							<div class="section__row">
								<div class="section__key">RUT-avdrag, 50%</div>
								<div class="section__value align-right">
									<?php echo $price->data->taxDeduction; ?> SEK <span>*</span>
								</div>
							</div>
							<?php
						}

						if ( ! empty( $price->data->grossPrice ) ) {
						?>
							<div class="section__row">
								<div class="section__key bold">Bruttopris</div>
								<div class="section__value align-right bold">
									<?php echo $price->data->grossPrice; ?> SEK <span>*</span>
								</div>
							</div>
							<?php
						}
						?>
						<div class="section__divider section__divider_gray"></div>
						<?php
						if ( ! empty( $price->data->transportPrice ) ) {
						?>
							<div class="section__row">
								<div class="section__key">Transport</div>
								<div class="section__value align-right">
									<?php echo $price->data->transportPrice; ?> SEK <span>*</span>
								</div>
							</div>
							<?php
						}

						if ( ! empty( $price->data->materialPrice ) ) {
						?>
							<div class="section__row">
								<div class="section__key">Material</div>
								<div class="section__value align-right">
									<?php echo $price->data->materialPrice; ?> SEK <span>*</span>
								</div>
							</div>
							<?php
						}

						if ( ! empty( $price->data->offerPrice ) ) {
						?>
							<div class="section__row">
								<div class="section__key bold">Du kommer att betala</div>
								<div class="section__value align-right bold red"><?php echo $price->data->offerPrice; ?> SEK</div>
							</div>
							<?php
						}
						?>
					</section>
				</div>
				<div class="container__row">
					<section class="container__section transparent">
						<div class="section__input-row">
							<label class="section__field-name" for="">
								<span style="color: #ba4246">*</span> Namn
							</label>
							<input
									required
									placeholder="Namn"
									name="name"
									type="text"
									class="input_text"
									value="<?php echo( ! empty( $price->data->name ) ? $price->data->name : '' ); ?>"
							/>
						</div>
						<div class="section__input-row section__input-row_two-column">
							<div>
								<label class="section__field-name" for="">
									<span style="color: #ba4246">*</span> Telefon
								</label>
								<input
										required
										id="accept-payment-page__input-phone"
										placeholder="Telefon"
										name="phone"
										type="tel"
										class="input_text"
										value="<?php echo( ! empty( $price->data->phone ) ? $price->data->phone : '' ); ?>"
								/>
							</div>
							<div>
								<label class="section__field-name" for="">
									<span style="color: #ba4246">*</span> Email
								</label>
								<input
										required
										id="accept-payment-page__input-email"
										placeholder="Email"
										name="email"
										type="email"
										class="input_text"
										value="<?php echo( ! empty( $price->data->email ) ? $price->data->email : '' ); ?>"
								/>
							</div>
						</div>
						<p class="section__field-name">Faktureringsaddress</p>
						<div class="section__input-row section__input-row_two-column">
							<label class="section__radio-label light" for="address1">
								<input
										checked
										class="accept-payment-page__invoice-address-radio small"
										id="address1"
										type="radio"
										name="invoiceAddress"
										value="<?php echo( ! empty( $price->data->fromAddress ) ? $price->data->fromAddress : '' ); ?>"
								/>
								<span>Flytta från<br/><span
											style="color: #555555;font-weight:700"><?php echo( ! empty( $price->data->fromAddress ) ? $price->data->fromAddress : '' ); ?></span></span>
							</label>
							<label class="section__radio-label light" for="address2">
								<input
										class="accept-payment-page__invoice-address-radio small"
										id="address2"
										type="radio"
										name="invoiceAddress"
										value="<?php echo( ! empty( $price->data->toAddress ) ? $price->data->toAddress : '' ); ?>"
								/>
								<span>Flytta till<br/><span
											style="color:#555555;font-weight:700"><?php echo( ! empty( $price->data->toAddress ) ? $price->data->toAddress : '' ); ?></span></span>
							</label>
							<label class="section__radio-label" for="accept-payment-page__invoice-another-address-radio">
								<input
										disable
										id="accept-payment-page__invoice-another-address-radio"
										type="radio"
										class="small"
										name="invoiceAddress"
										value="{value}"
								/>
								Använd en annan adress
							</label>
							<div class="grid-item_span-2">
								<input
										name="fromAddressJson"
										type="text"
										class="input_text"
										style="display: none"
										id="fromAddressJson"
								/>
								<input
										disabled
										placeholder="Faktureringsadress"
										name="fromAddress"
										type="text"
										class="input_text"
										id="fromAddress"
								/>
								<div class="request-form__field-error_container">
									<span class="request-form__field-error invisible">Error</span>
								</div>
								<ul
										class="request-form__suggestions-fromAddress hide"
										id="request-form__suggestions-fromAddress"
								></ul>
							</div>
						</div>

						<div class="section__input-row">
							<label class="section__radio-label" for="info">
								<input
									required
									class="input_checkbox small"
									type="checkbox"
									name="confirm"
									value="info"
									id="info"
								/>
								Jag bekräftar härmed att all information som anges i erbjudandet stämmer, såsom volym,
								gångavstånd och våning.
							</label>
						</div>
						<div class="section__input-row">
							<label class="section__radio-label" for="terms">
								<input
									required
									class="input_checkbox small"
									type="checkbox"
									name="terms_confirm"
									value="terms"
									id="terms"
								/>
								<span>Jag bekräftar härmed att jag har läst Steg Logistics <a href="https://steglogistic.se/allmanna-villkor/" target="_blank" style="text-decoration: underline;">Allmänna villkor</a>.</span>
							</label>
						</div>
						<div class="section__input-row">
							<input type="hidden" name="offerPrice" value="<?php echo $price->data->offerPrice; ?>"/>
							<button id="accept-payment-page__submit-button" class="button" type="button">Acceptera med bank-ID</button>
						</div>
					</section>
				</div>
			</div>
		</div>
	</div>
<?php
get_footer();
