<?php
/* Template Name: Feedback */

if ( ! empty( $_GET['taskKey'] ) ) {
	$task_id = FD_Moving_Request::get_task_id( $_GET['taskKey'] );
}
get_header();
?>
	<div class="feedback-page" id="feedback-page">
		<div id="feedback-page__feedback-form" class="page__container">
			<div class="container__header container__header_center">
				<h1 class="container__title container__title_center" id="feedback-page-title">Din flytt beställning har slutförts.<br/> Hur nöjd är du?</h1>
				<?php
				if ( isset( $task_id ) ) {
					?>
					<h2 class="container__id">ID <?php echo sprintf( "%06d", $task_id ); ?></h2>
					<?php
				}
				?>
			</div>
			<div class="container__content">
				<div class="container__row">
					<section class="container__section">
						<h3 class="section__title" id="feedback-page-subtitle">Respons</h3>
						<div class="section__divider"></div>
						<div class="section__row underline">
							<div class="section__key">Pris</div>
							<div class="section__value">
								<div class="input-rating__container">
									<label style="display:none" for="price-rating-0"></label><input checked type="radio" id="price-rating-0" name="PriceFeedback" value="Empty">
									<label for="price-rating-1"></label><input type="radio" id="price-rating-1" name="PriceFeedback" value="VeryPoor">
									<label for="price-rating-2"></label><input type="radio" id="price-rating-2" name="PriceFeedback" value="Poor">
									<label for="price-rating-3"></label><input type="radio" id="price-rating-3" name="PriceFeedback" value="Average">
									<label for="price-rating-4"></label><input type="radio" id="price-rating-4" name="PriceFeedback" value="Good">
									<label for="price-rating-5"></label><input type="radio" id="price-rating-5" name="PriceFeedback" value="Excellent">
								</div>
							</div>
						</div>
						<div class="section__row underline">
							<div class="section__key">Kundservice</div>
							<div class="section__value">
								<div class="input-rating__container">
									<label style="display:none" for="customer-rating-0"></label><input checked type="radio" id="customer-rating-0" name="CustomerServiceFeedback" value="Empty">
									<label for="customer-rating-1"></label><input type="radio" id="customer-rating-1" name="CustomerServiceFeedback" value="VeryPoor">
									<label for="customer-rating-2"></label><input type="radio" id="customer-rating-2" name="CustomerServiceFeedback" value="Poor">
									<label for="customer-rating-3"></label><input type="radio" id="customer-rating-3" name="CustomerServiceFeedback" value="Average">
									<label for="customer-rating-4"></label><input type="radio" id="customer-rating-4" name="CustomerServiceFeedback" value="Good">
									<label for="customer-rating-5"></label><input type="radio" id="customer-rating-5" name="CustomerServiceFeedback" value="Excellent">
								</div>
							</div>
						</div>
						<div class="section__row underline margin-bottom_16">
							<div class="section__key">Jobbservice</div>
							<div class="section__value">
								<div class="input-rating__container">
									<label style="display:none" for="job-rating-0"></label><input checked type="radio" id="job-rating-0" name="JobServiceFeedback" value="Empty">
									<label for="job-rating-1"></label><input type="radio" id="job-rating-1" name="JobServiceFeedback" value="VeryPoor">
									<label for="job-rating-2"></label><input type="radio" id="job-rating-2" name="JobServiceFeedback" value="Poor">
									<label for="job-rating-3"></label><input type="radio" id="job-rating-3" name="JobServiceFeedback" value="Average">
									<label for="job-rating-4"></label><input type="radio" id="job-rating-4" name="JobServiceFeedback" value="Good">
									<label checked for="job-rating-5"></label><input type="radio" id="job-rating-5" name="JobServiceFeedback" value="Excellent">
								</div>
							</div>
						</div>
						<div class="section__row columns-1 padding-0 margin-bottom_16">
							<label class="section__field-name" for="">
								Din kommentar
							</label>
							<textarea
									class="input_text"
									name="comment"
									cols="30"
									rows="10"
							></textarea>
						</div>
						<div style="display:none; margin-bottom:24px;" class="section__row columns-2-auto padding-0"
							 id="file-input-container">
							<div class="section__field-name">
								Bilder på skador:
							</div>
							<label class="input-file" for="feedback-page__attachment-input">
								Bifoga
								<input multiple accept="image/*" style="opacity: 0" class="input_file" type="file"
									   id="feedback-page__attachment-input" name="files">
							</label>
						</div>
						<div class="input-file__attachment-container" id="feedback-page__attachment-container">
						</div>
						<div class="section__divider section__divider_gray"></div>
						<div class="section__input-row margin-bottom-0">
							<button id="feedback-page__submit-button" style="min-width: 189px" class="button">Skicka in</button>
						</div>

					</section>
				</div>
			</div>
			<div class="container__footer" id="feedback-page-footer">
				<h2 class="footer__text">Du kan också skicka ett klagomål om något har gått fel</h2>
				<button style="min-width: 189px" class="button outline" id="complain-mode-button">Klagomål</button>
			</div>
		</div>
		<div id="feedback-page__success-message" class="page__container flex" style="display: none">
			<div class="container__header container__header_center">
					<h1 class="container__title container__title_center" id="feedback-page-title">Tack för din feedback!</h1>
					<?php
					if ( isset( $task_id ) ) {
						?>
						<h2 class="container__id">ID <?php echo sprintf( "%06d", $task_id ); ?></h2>
						<?php
					}
					?>
			</div>
			<div class="container__footer">
				<button id="feedback-page__home-button" class="button">Startsida</button>
			</div>
		</div>
	</div>
<?php
get_footer();
