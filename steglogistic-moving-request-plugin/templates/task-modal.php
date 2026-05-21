<div class="modal__overlay closed" id="modal_request_form">
	<div class="modal__popup">
	<button
		type="button"
		class="modal__close-button"
		id="modal_request_form_button_close"
	>
		<svg
		width="24"
		height="24"
		viewBox="0 0 24 24"
		fill="none"
		xmlns="http://www.w3.org/2000/svg"
		>
		<path
			d="M18.5312 16.8633C18.918 17.293 18.918 17.9375 18.5312 18.3242C18.1016 18.7539 17.457 18.7539 17.0703 18.3242L12 13.2109L6.88672 18.3242C6.45703 18.7539 5.8125 18.7539 5.42578 18.3242C4.99609 17.9375 4.99609 17.293 5.42578 16.8633L10.5391 11.75L5.42578 6.63672C4.99609 6.20703 4.99609 5.5625 5.42578 5.17578C5.8125 4.74609 6.45703 4.74609 6.84375 5.17578L12 10.332L17.1133 5.21875C17.5 4.78906 18.1445 4.78906 18.5312 5.21875C18.9609 5.60547 18.9609 6.25 18.5312 6.67969L13.418 11.75L18.5312 16.8633Z"
			fill="#999999"
		/>
		</svg>
	</button>
	<div class="modal__content">
		<form id="request-form" name="request-form" autocomplete="on">
		<div
			class="request-form request-form__step request-form__step-1 active"
		>
			<h1 class="modal__h1">Flytta från</h1>
			<h2 class="modal__h2">Steg 1 av 4</h2>
			<div class="request-form__row request-form__first-row">
			<div class="request-form__first-row_div1">
				<label class="request-form__field-name" for="fromCustomAddress">
				<span style="color: #ba4246">*</span> Utflyttningsadress
				</label>
				<input
				required
				placeholder="Utflyttningsadress"
				name="fromCustomAddress"
				type="text"
				class="input_text"
				id="fromCustomAddress"
				/>
			</div>
			<div class="request-form__first-row_div2">
				<label class="request-form__field-name" for="fromPostCode">
				<span style="color: #ba4246">*</span> Postnummer
				</label>
				<input
				required
				placeholder="Postnummer"
				name="fromPostCode"
				type="text"
				class="input_text"
				id="fromPostCode"
				/>
			</div>
			<div class="request-form__first-row_div3">
				<label class="request-form__field-name" for="fromCity">
				<span style="color: #ba4246">*</span> Stad
				</label>
				<input
				required
				placeholder="Stad"
				name="fromCity"
				type="text"
				class="input_text"
				id="fromCity"
				/>
			</div>
			</div>
			<div class="request-form__row request-form__second-row">
			<div>
				<label
				class="request-form__field-name"
				for="fromAccommodationType"
				>
				<span style="color: #ba4246">*</span> Bostadstyp
				</label>
				<select
				required
				class="input_select"
				name="fromAccommodationType"
				id="fromAccommodationType"
				>
				<option value="" disabled selected hidden>Bostadstyp</option>
				<option value="Apartment">Lägenhet</option>
				<option value="TerracedHouse">Radhus</option>
				<option value="House">Villa</option>
				<option value="Storehouse">Förråd</option>
				</select>
			</div>
			<div>
				<label class="request-form__field-name" for="fromSize">
				<span style="color: #ba4246">*</span> Boyta, m2
				</label>
				<input
				required
				name="fromSize"
				id="fromSize"
				placeholder="Boyta, m2"
				min="1"
				max="9999"
				type="number"
				class="input_text"
				/>
			</div>
			<div>
				<label class="request-form__field-name" for="fromFloor">
				<span style="color: #ba4246">*</span> Våning
				</label>
				<select
				required
				class="input_select"
				name="fromFloor"
				id="fromFloor"
				>
				<option value="" disabled selected hidden>Våning</option>
				<option value="0">0</option>
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5 eller uppåt</option>
				</select>
			</div>
			<div>
				<label class="request-form__field-name" for="fromElevator">
				<span style="color: #ba4246">*</span> Hiss
				</label>
				<select
				required
				class="input_select"
				name="fromElevator"
				id="fromElevator"
				>
				<option value="" disabled selected hidden>Hiss</option>
				<option value="NoElevator">Nej</option>
				<option value="Two">2 personer</option>
				<option value="Four">4 personer</option>
				<option value="Six">6 personer</option>
				<option value="Eight">8 personer</option>
				</select>
			</div>
			<div>
				<label class="request-form__field-name" for="fromLoadingDistance">
				<span style="color: #ba4246">*</span> Gångavstånd
				</label>
				<select
				required
				class="input_select"
				name="fromLoadingDistance"
				id="fromLoadingDistance"
				>
				<option value="" disabled selected hidden>Gångavstånd</option>
				<option value="m0_10">0-10 m</option>
				<option value="m10_20">10-20 m</option>
				<option value="m20_30">20-30 m</option>
				<option value="m30_50">30-50 m</option>
				<option value="m50_">50+ m</option>
				</select>
			</div>
			</div>
			<fieldset class="request-form__row request-form__third-row">
			<legend class="request-form__field-name">Möbelmängd</legend>
			<div class="request-form__checkbox-container">
				<label for="furniture-quantity__lite">
				<input
					type="radio"
					class="small"
					name="furnitureAmount"
					value="Small"
					id="furniture-quantity__lite"
				/>
				Sparsamt
				</label>
				<label for="furniture-quantity__normalt">
				<input
					type="radio"
					class="small"
					name="furnitureAmount"
					value="Medium"
					id="furniture-quantity__normalt"
				/>
				Normalt
				</label>
				<label for="furniture-quantity__mycket">
				<input
					type="radio"
					class="small"
					name="furnitureAmount"
					value="Large"
					id="furniture-quantity__mycket"
				/>
				Välmöblerat
				</label>
			</div>
			</fieldset>
			<fieldset class="request-form__row request-form__third-row">
			<legend class="request-form__field-name">Förråd</legend>
			<div class="request-form__checkbox-container">
				<label for="storage-room__no">
				<input
					type="radio"
					class="small"
					name="storageUnit"
					value="no"
					id="storage-room__no"
				/>
				Nej
				</label>
				<label for="storage-room__yes">
				<input
					type="radio"
					class="small"
					name="storageUnit"
					value="yes"
					id="storage-room__yes"
				/>
				Ja
				</label>
			</div>
			</fieldset>
			<fieldset class="request-form__row request-form__third-row">
			<legend class="request-form__field-name">
				Ytterligare tjänster
			</legend>
			<div class="request-form__checkbox-container">
				<label for="additional-service__packing">
				<input
					type="checkbox"
					class="small"
					name="additionalService"
					value="Packing"
					id="additional-service__packing"
				/>
				Packning i kartonger
				</label>
				<label for="additional-service__dismantling">
				<input
					type="checkbox"
					class="small"
					name="additionalService"
					value="Disassembling"
					id="additional-service__dismantling"
				/>
				Nedmontering av stora möbler
				</label>
			</div>
			</fieldset>
			<div class="request-form__row request-form__forth-row">
			<input
				required
				name="jobDate"
				id="movingJobDate"
				class="input_text moving_date max-width_390px"
				type="text"
				placeholder="Flyttdatum"
			/>
			</div>
			<div class="request-form__row request-form__fifth-row">
			<button type="button" class="button request-form__next-button">
				Nästa
			</button>
			</div>
			<div class="request-form__forth-row"></div>
		</div>
		<div class="request-form request-form__step request-form__step-2">
			<h1 class="modal__h1">Städning</h1>
			<h2 class="modal__h2">Steg 2 av 4</h2>
			<div class="request-form__second-step-content">
			<img
				src="<?php echo FD_SMR_PLUGIN_URL . '/images/standing.svg'; ?>"
				alt=""
			/>
			<p class="request-form__second-step-head">Städning</p>
			<p class="request-form__second-step-text">
				Om du beställer städningen tillsammans med flytten som ett paket
				får du
				<span style="color: #ba4246; font-size: 18px; font-weight: bold"
				>20%</span
				>
				rabatt på städningen. <br />
				Behöver du flyttstädning? 
			</p>
			<div id="activatedCleaningFormButtons">
				<button
				type="button"
				class="button outline request-form__next-button"
				id="cleaning__false"
				>
				Nej
				</button>
				<button
				type="button"
				class="button"
				id="cleaning__true"
				>
				Ja
				</button>
			</div>
			<input
				style="display: none"
				type="radio"
				name="cleaning"
				id="cleaning"
			/>
			</div>
			<div id="cleaningForm" class="request-form__row request-form__row_cleaning" style="display:none;gap:16px">
			<div style="width: 100%;">
				<input
				name="cleaningJobDate"
				id="cleaningJobDate"
				class="input_text cleaning_date max-width_390px"
				type="text"
				placeholder="Välj datum"
				/>
			</div>
			<div style="width: 100%;">
				<textarea
				class="input_textarea"
				id="cleaningJobComment"
				name="cleaningComment"
				placeholder="Något vi behöver veta om din städning?"
				cols="30"
				rows="3"
				></textarea>
			</div>
			</div>
			<div class="request-form__row request-form__sixth-row">
			<button
				type="button"
				class="div1 button outline request-form__back-button"
			>
				Tillbaka
			</button>
			<button
				type="button"
				class="div2 button request-form__next-button"
				id="cleaning__next-button"
				style="display:none"
			>
				Nästa
			</button>
			<button id="deactivateCleaningButton" type="button" class="div3 button outline request-form__cancel-cleaning">
				Ta bort städning
			</button>
			</div>
		</div>
		<div class="request-form request-form__step request-form__step-3">
			<h1 class="modal__h1">Flytta till</h1>
			<h2 class="modal__h2">Steg 3 av 4</h2>
			<div class="request-form__row request-form__first-row">
			<div class="request-form__first-row_div1">
				<label class="request-form__field-name" for="toCustomAddress">
				<span style="color: #ba4246">*</span> Inflyttningsadress
				</label>
				<input
				placeholder="Inflyttningsadress"
				required
				name="toCustomAddress"
				type="text"
				class="input_text"
				id="toCustomAddress"
				/>
			</div>
			<div class="request-form__first-row_div2">
				<label class="request-form__field-name" for="toPostCode">
				<span style="color: #ba4246">*</span> Postnummer
				</label>
				<input
				placeholder="Postnummer"
				required
				name="toPostCode"
				type="text"
				class="input_text"
				id="toPostCode"
				/>
			</div>
			<div class="request-form__first-row_div3">
				<label class="request-form__field-name" for="toCity">
				<span style="color: #ba4246">*</span> Stad
				</label>
				<input
				placeholder="Stad"
				required
				name="toCity"
				type="text"
				class="input_text"
				id="toCity"
				/>
			</div>
			</div>

			<div class="request-form__row request-form__second-row">
			<div>
				<label class="request-form__field-name" for="">
				<span style="color: #ba4246">*</span> Bostadstyp
				</label>
				<select
				required
				class="input_select"
				name="toAccommodationType"
				id=""
				>
				<option value="" disabled selected hidden>Bostadstyp</option>
				<option value="Apartment">Lägenhet</option>
				<option value="TerracedHouse">Radhus</option>
				<option value="House">Villa</option>
				<option value="Storehouse">Förråd</option>
				</select>
			</div>
			<div>
				<label class="request-form__field-name" for="">
				<span style="color: #ba4246">*</span> Boyta, m2
				</label>
				<input
				required
				name="toSize"
				placeholder="Boyta, m2"
				type="number"
				min="1"
				max="9999"
				class="input_text"
				/>
			</div>
			<div>
				<label class="request-form__field-name" for="">
				<span style="color: #ba4246">*</span> Våning
				</label>
				<select required class="input_select" name="toFloor" id="">
				<option value="" disabled selected hidden>Våning</option>
				<option value="0">0</option>
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5 eller uppåt</option>
				</select>
			</div>
			<div>
				<label class="request-form__field-name" for="">
				<span style="color: #ba4246">*</span> Hiss
				</label>
				<select required class="input_select" name="toElevator" id="">
				<option value="" disabled selected hidden>Hiss</option>
				<option value="NoElevator">Nej</option>
				<option value="Two">2 personer</option>
				<option value="Four">4 personer</option>
				<option value="Six">6 personer</option>
				<option value="Eight">8 personer</option>
				</select>
			</div>
			<div>
				<label class="request-form__field-name" for="">
				<span style="color: #ba4246">*</span> Gångavstånd
				</label>
				<select
				required
				class="input_select"
				name="toLoadingDistance"
				id=""
				>
				<option value="" disabled selected hidden>Gångavstånd</option>
				<option value="m0_10">0-10 m</option>
				<option value="m10_20">10-20 m</option>
				<option value="m20_30">20-30 m</option>
				<option value="m30_50">30-50 m</option>
				<option value="m50_">50+ m</option>
				</select>
			</div>
			</div>
			<fieldset class="request-form__row request-form__third-row">
			<legend class="request-form__field-name">
				Ytterligare tjänster
			</legend>
			<div class="request-form__checkbox-container">
				<label for="additional-service__unpacking">
				<input
					type="checkbox"
					class="small"
					name="additionalService"
					value="Unpacking"
					id="additional-service__unpacking"
				/>
				Uppackning av kartonger
				</label>
				<label for="additional-service__assembling">
				<input
					type="checkbox"
					class="small"
					name="additionalService"
					value="Assembling"
					id="additional-service__assembling"
				/>
				Montering av stora möbler
				</label>
			</div>
			</fieldset>
			<div class="request-form__row request-form__fifth-row">
			<button
				type="button"
				class="button outline request-form__back-button"
			>
				Tillbaka
			</button>
			<button type="button" class="button request-form__next-button">
				Nästa
			</button>
			</div>
		</div>
		<div class="request-form request-form__step request-form__step-4">
			<h1 class="modal__h1">Ytterligare detaljer</h1>
			<h2 class="modal__h2">Steg 4 av 4</h2>
			<div class="request-form__row">
			<label class="request-form__field-name" for=movingJobComment">
				Övrig information som du vill lämna till oss
			</label>
			<textarea
				id="movingJobComment"
				class="input_text"
				name="movingComment"
				cols="30"
				rows="10"
			></textarea>
			</div>
			<div class="request-form__row request-form__fifth-row">
			<button
				type="button"
				class="button outline request-form__back-button"
			>
				Tillbaka
			</button>
			<button type="button" class="button request-form__send-button">
				Nästa
			</button>
			</div>
		</div>
		</form>
	</div>
	</div>
</div>
