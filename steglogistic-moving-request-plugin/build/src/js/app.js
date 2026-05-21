// window.addEventListener('DOMContentLoaded', function () {
//   const eventEmitter = new EventEmitter();
//   new Modal('modal_request_form', eventEmitter);
//   new RequestForm(eventEmitter);
// });
// *! fix "Uncaught TypeError: Cannot read properties of null (reading 'getElementsByTagName')"

document.addEventListener('DOMContentLoaded', function () {
  const heroPage = document.getElementById('request-form_short');

  if (heroPage) {
    const eventEmitter = new EventEmitter();
    new Modal('modal_request_form', eventEmitter);
    new RequestForm(eventEmitter);

    flatpickr('.moving_date', {
      dateFormat: 'Y-m-d',
      minDate: new Date(),
      disableMobile: true,
      defaultDate: SessionStorageService.getItem('jobDate') || undefined
    });

    flatpickr('.cleaning_date', {
      dateFormat: 'Y-m-d',
      minDate: new Date(),
      disableMobile: true,
      defaultDate: SessionStorageService.getItem('cleaningJobDate') || undefined
    });
  }
});

document.addEventListener('DOMContentLoaded', function () {
  const summaryPage = document.getElementById('summary-page');
  if (summaryPage) {
    renderSummary();
    addEventListenerToClosePageButton();
    new Modal('modal_success_task');
  }
});

document.addEventListener('DOMContentLoaded', function () {
  const feedbackPage = document.getElementById('feedback-page');
  if (feedbackPage) {
    new FeedbackPage(feedbackPage);
  }
});

document.addEventListener('DOMContentLoaded', function () {
  const acceptPaymentPage = document.getElementById('accept-payment-page');
  if (acceptPaymentPage) {
    const eventEmitter = new EventEmitter();
    new AcceptPaymentPage(eventEmitter);
    new Modal('modal_accept_payment', eventEmitter);
  }
});

//** #region google place autocomplete service
// function _hideFromAddressSuggestionsContainer() {
//   const suggestionsContainer = document.getElementById('request-form__suggestions-fromAddress');
//   suggestionsContainer.classList.add('hide');
//   document.removeEventListener('click', _hideFromAddressSuggestionsContainer);
// }
//
// function _hideToAddressSuggestionsContainer() {
//   const suggestionsContainer = document.getElementById('request-form__suggestions-toAddress');
//   suggestionsContainer.classList.add('hide');
//   document.removeEventListener('click', _hideToAddressSuggestionsContainer);
// }
//
// function _getAddressComponent(place, type) {
//   return place.address_components.find(component => component.types.includes(type))?.long_name || '';
// }
//
// function initGoogleService() {
//   const autocompleteService = new google.maps.places.AutocompleteService();
//   const placeService = new google.maps.places.PlacesService(document.createElement('div'));
//   const fromAddressInput = document.getElementById('fromAddress');
//   const fromAddressInputJson = document.getElementById('fromAddressJson');
//   const toAddressInput = document.getElementById('toAddress');
//   const toAddressInputJson = document.getElementById('toAddressJson');
//   const suggestionsContainerFromAddress = document.getElementById('request-form__suggestions-fromAddress');
//   const suggestionsContainerToAddress = document.getElementById('request-form__suggestions-toAddress');
//
//   const displaySuggestions = function (input, addressInputJson, suggestionsContainer) {
//     return (predictions, status) => {
//       fromAddressInput.classList.remove('loading');
//       toAddressInput.classList.remove('loading');
//       if (status !== google.maps.places.PlacesServiceStatus.OK || !predictions) {
//         alert(status);
//         return;
//       }
//
//       suggestionsContainer.classList.remove('hide');
//       document.addEventListener('click', _hideFromAddressSuggestionsContainer);
//       document.addEventListener('click', _hideToAddressSuggestionsContainer);
//
//       while (suggestionsContainer.firstChild) {
//         suggestionsContainer.removeChild(suggestionsContainer.firstChild);
//       }
//
//       predictions.forEach(prediction => {
//         const li = document.createElement('li');
//
//         li.appendChild(document.createTextNode(prediction.description));
//         li.addEventListener('click', () => {
//           input.value = prediction.description;
//
//           placeService.getDetails(
//             {
//               placeId: prediction.place_id
//             },
//             function (place, status) {
//               if (status === google.maps.places.PlacesServiceStatus.OK) {
//                 const postCode = _getAddressComponent(place, 'postal_code');
//                 const country = _getAddressComponent(place, 'country');
//                 const state = _getAddressComponent(place, 'administrative_area_level_1');
//                 const city =
//                   _getAddressComponent(place, 'postal_town') ||
//                   _getAddressComponent(place, 'locality') ||
//                   _getAddressComponent(place, 'political');
//                 const street = _getAddressComponent(place, 'route');
//                 const building = _getAddressComponent(place, 'street_number');
//                 const JSONAddress = JSON.stringify({postCode, country, state, city, street, building});
//                 addressInputJson.value = JSONAddress;
//               }
//             }
//           );
//         });
//         suggestionsContainer.appendChild(li);
//       });
//     };
//   };
//
//   fromAddressInput.addEventListener('input', event => {
//     if (event.currentTarget.value) {
//       fromAddressInput.classList.add('loading');
//     }
//
//     autocompleteService.getPlacePredictions(
//       {input: event.currentTarget.value},
//       displaySuggestions(fromAddressInput, fromAddressInputJson, suggestionsContainerFromAddress)
//     );
//   });
//
//   toAddressInput.addEventListener('input', event => {
//     if (event.currentTarget.value) {
//       toAddressInput.classList.add('loading');
//     }
//
//     autocompleteService.getPlacePredictions(
//       {input: event.currentTarget.value},
//       displaySuggestions(toAddressInput, toAddressInputJson, suggestionsContainerToAddress)
//     );
//   });
// }
//
// window.initService = initGoogleService;
//** #endregion
