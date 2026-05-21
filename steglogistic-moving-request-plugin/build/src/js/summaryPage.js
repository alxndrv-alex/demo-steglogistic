function renderFloor(floor) {
  switch (floor) {
    case 5:
      return `${floor} eller uppåt`;
    case '5':
      return `${floor} eller uppåt`;
    default:
      return floor;
  }
}

function _getAddress(value, isLast) {
  return value.trim() ? `${value}${isLast ? '' : ', '}` : '';
}

function renderSummary() {
  const summaryPage = document.getElementById('summary-container');

  let summaryContentPage = `
  <div class="summary-header">
    <h1 class="summary-title">Sammanfattning</h1>
    <h2 class="summary-id">ID 0000{taskId}</h2>
  </div>
  <div class="summary-content">
  <div class="summary-row">
    <section class="summary-section">
      <h3 class="summary-section__title">Kontaktinformation</h3>
      <div class="summary-section__divider"></div>
      <div class="summary-section__row">
        <div class="summary-section__key">Namn</div>
        <div class="summary-section__value">{name}</div>
      </div>
      <div class="summary-section__row">
        <div class="summary-section__key">Telefon</div>
        <div class="summary-section__value">{phone}</div>
      </div>
      <div class="summary-section__row">
        <div class="summary-section__key">E-post</div>
        <div class="summary-section__value">{email}</div>
      </div>
    </section>
  </div>
  <div class="summary-row_2">
    <section class="summary-section" id="fromAddress">
      <div class="summary-section__key">Flytta från</div>
      <h3 class="summary-section__title">{fromAddress}</h3>
      <div class="summary-section__divider summary-section__divider_gray"></div>
      <div class="summary-section__row">
        <div class="summary-section__key">Bostadstyp</div>
        <div class="summary-section__value">{fromType}</div>
      </div>
      <div class="summary-section__row">
        <div class="summary-section__key">Boyta</div>
        <div class="summary-section__value">{fromSize}</div>
      </div>
      <div class="summary-section__row">
        <div class="summary-section__key">Våning</div>
        <div class="summary-section__value">{fromFloor}</div>
      </div>
      <div class="summary-section__row">
        <div class="summary-section__key">Hiss</div>
        <div class="summary-section__value">{fromElevator}</div>
      </div>
      <div class="summary-section__row">
        <div class="summary-section__key">Gångavstånd</div>
        <div class="summary-section__value">{fromLoadingDistance}</div>
      </div>
      <div class="summary-section__row">
        <div class="summary-section__key">Möbelmängd</div>
        <div class="summary-section__value">{furnitureAmount}</div>
      </div>
      <div class="summary-section__row">
        <div class="summary-section__key">Förråd</div>
        <div class="summary-section__value">{storageUnit}</div>
      </div>
    </section>
    <section class="summary-section">
      <div class="summary-section__key">Flytta till</div>
      <h3 class="summary-section__title">{toAddress}</h3>
      <div class="summary-section__divider summary-section__divider_gray"></div>
      <div class="summary-section__row">
        <div class="summary-section__key">Bostadstyp</div>
        <div class="summary-section__value">{toType}</div>
      </div>
      <div class="summary-section__row">
        <div class="summary-section__key">Boyta</div>
        <div class="summary-section__value">{toSize}</div>
      </div>
      <div class="summary-section__row">
        <div class="summary-section__key">Våning</div>
        <div class="summary-section__value">{toFloor}</div>
      </div>
      <div class="summary-section__row">
        <div class="summary-section__key">Hiss</div>
        <div class="summary-section__value">{toElevator}</div>
      </div>
      <div class="summary-section__row">
        <div class="summary-section__key">Gångavstånd</div>
        <div class="summary-section__value">{toLoadingDistance}</div>
      </div>
    </section>
  </div>
  <div class="summary-row">
    <div style="display:flex;gap:16px">
      <section class="summary-section">
        <h3 class="summary-section__title">Flyttdatum</h3>
        <div class="summary-section__divider"></div>
        <div class="summary-section__row">
          <div class="summary-section__key">Flyttdatum</div>
          <div class="summary-section__value">{jobDate}</div>
        </div>
      </section>
    </div>
  </div>
  <div class="summary-row" id="extra-services">
  </div>
  <div class="summary-row">
    <div style="display:flex;gap:16px">
      <section class="summary-section comment">
        <h3 class="summary-section__title">Flyttkommentar</h3>
        <div class="summary-section__divider summary-section__divider_gray"></div>
        <p class="summary-section__comment">{movingComment}</p>
      </section>
    </div>
  </div>
  </div>
  <div class="summary-footer">
  <button type="button" class="button outline" id="goback-to-request-form" onclick="goBack()">
    Tillbaka
  </button>
  <button type="button" class="button" id="send-request-button" onclick="updateRequest(event)">
    Skicka förfrågan
  </button>
</div>`;

  let summaryContentPageWithCleaning = `
  <div class="summary-header">
    <h1 class="summary-title">Sammanfattning</h1>
    <h2 class="summary-id">ID 0000{taskId}</h2>
  </div>
  <div class="summary-content">
  <div class="summary-row">
    <section class="summary-section">
      <h3 class="summary-section__title">Kontaktinformation</h3>
      <div class="summary-section__divider"></div>
      <div class="summary-section__row">
        <div class="summary-section__key">Namn</div>
        <div class="summary-section__value">{name}</div>
      </div>
      <div class="summary-section__row">
        <div class="summary-section__key">Telefon</div>
        <div class="summary-section__value">{phone}</div>
      </div>
      <div class="summary-section__row">
        <div class="summary-section__key">E-post</div>
        <div class="summary-section__value">{email}</div>
      </div>
    </section>
  </div>
  <div class="summary-row_2">
    <section class="summary-section" id="fromAddress">
      <div class="summary-section__key">Flytta från</div>
      <h3 class="summary-section__title">{fromAddress}</h3>
      <div class="summary-section__divider summary-section__divider_gray"></div>
      <div class="summary-section__row">
        <div class="summary-section__key">Bostadstyp</div>
        <div class="summary-section__value">{fromType}</div>
      </div>
      <div class="summary-section__row">
        <div class="summary-section__key">Boyta</div>
        <div class="summary-section__value">{fromSize}</div>
      </div>
      <div class="summary-section__row">
        <div class="summary-section__key">Våning</div>
        <div class="summary-section__value">{fromFloor}</div>
      </div>
      <div class="summary-section__row">
        <div class="summary-section__key">Hiss</div>
        <div class="summary-section__value">{fromElevator}</div>
      </div>
      <div class="summary-section__row">
        <div class="summary-section__key">Gångavstånd</div>
        <div class="summary-section__value">{fromLoadingDistance}</div>
      </div>
      <div class="summary-section__row">
        <div class="summary-section__key">Möbelmängd</div>
        <div class="summary-section__value">{furnitureAmount}</div>
      </div>
      <div class="summary-section__row">
        <div class="summary-section__key">Förråd</div>
        <div class="summary-section__value">{storageUnit}</div>
      </div>
    </section>
    <section class="summary-section">
      <div class="summary-section__key">Flytta till</div>
      <h3 class="summary-section__title">{toAddress}</h3>
      <div class="summary-section__divider summary-section__divider_gray"></div>
      <div class="summary-section__row">
        <div class="summary-section__key">Bostadstyp</div>
        <div class="summary-section__value">{toType}</div>
      </div>
      <div class="summary-section__row">
        <div class="summary-section__key">Boyta</div>
        <div class="summary-section__value">{toSize}</div>
      </div>
      <div class="summary-section__row">
        <div class="summary-section__key">Våning</div>
        <div class="summary-section__value">{toFloor}</div>
      </div>
      <div class="summary-section__row">
        <div class="summary-section__key">Hiss</div>
        <div class="summary-section__value">{toElevator}</div>
      </div>
      <div class="summary-section__row">
        <div class="summary-section__key">Gångavstånd</div>
        <div class="summary-section__value">{toLoadingDistance}</div>
      </div>
    </section>
  </div>
  <div class="summary-row">
    <div class="summary-row__container">
      <section class="summary-section">
        <h3 class="summary-section__title">Flyttdatum</h3>
        <div class="summary-section__divider"></div>
        <div class="summary-section__row">
          <div class="summary-section__key">Flyttdatum</div>
          <div class="summary-section__value">{jobDate}</div>
        </div>
      </section>
      <section class="summary-section">
        <h3 class="summary-section__title">Städningdatum</h3>
        <div class="summary-section__divider"></div>
        <div class="summary-section__row">
          <div class="summary-section__key">Datum</div>
          <div class="summary-section__value">{cleaningJobDate}</div>
        </div>
      </section>
    </div>
  </div>
  <div class="summary-row">
    <div class="summary-row__container">
      <section class="summary-section comment">
        <h3 class="summary-section__title">Flyttkommentar</h3>
        <div class="summary-section__divider summary-section__divider_gray"></div>
        <p class="summary-section__comment">{movingComment}</p>
      </section>
      <section class="summary-section comment">
        <h3 class="summary-section__title">Städkommentar</h3>
        <div class="summary-section__divider summary-section__divider_gray"></div>
        <p class="summary-section__comment">{cleaningComment}</p>
      </section>
    </div>
  </div>
  <div class="summary-row" id="extra-services">
  </div>
  </div>
  <div class="summary-footer">
  <button type="button" class="button outline" id="goback-to-request-form" onclick="goBack()">
    Tillbaka
  </button>
  <button type="button" class="button" id="send-request-button" onclick="updateRequest(event)">
    Skicka förfrågan
  </button>
</div>`;

  const fieldsArr = [
    'name',
    'phone',
    'email',
    'taskId',
    'taskKey',
    'movingComment',
    'storageUnit',
    'jobDate',
    'extraServices',
    'cleaning',
    'fromAddress',
    'toAddress',
    'furnitureAmount',
    'cleaningJobDate',
    'cleaningComment'
  ];

  const accommodationType = {Apartment: 'Lägenhet', TerracedHouse: 'Radhus', House: 'Villa', Storehouse: 'Förråd'};
  const loadingDistance = {m0_10: '0-10 m', m10_20: '10-20 m', m20_30: '20-30 m', m30_50: '30-50 m', m50_: '50+ m'};
  const elevator = {NoElevator: 'Nej', Two: '2 personer', Four: '4 personer', Six: '6 personer', Eight: '8 personer'};
  const furnitureAmount = {
    Small: 'Sparsamt',
    Medium: 'Normalt',
    Large: ' Välmöblerat'
  };
  const extraServices = {
    Packing: {
      extraServiceName: 'Packning i kartonger',
      extraServiceDescription: 'Vår flyttpersonal kommer att ta hand om packningen av de flyttkartonger vi kommit överens om.'
    },
    Disassembling: {
      extraServiceName: 'Nedmontering av stora möbler',
      extraServiceDescription: 'Vår flyttpersonal kommer att montera ned dem möbler vi kommit överens om på utflyttningsadressen.'
    },
    Cleaning: {extraServiceName: 'Städning', extraServiceDescription: 'Vi ser till att flyttstäda åt dig.'},
    Unpacking: {
      extraServiceName: 'Uppackning av kartonger',
      extraServiceDescription: 'Vår flyttpersonal packar upp alla saker som tidigare packats.'
    },
    Assembling: {
      extraServiceName: 'Montering av stora möbler',
      extraServiceDescription: 'Vår flyttpersonal kommer att montera ihop dem möbler vi kommit överens om på inflyttningsadressen.'
    }
  };

  if (summaryPage) {
    const data = fieldsArr.reduce((acc, curr) => {
      const value = SessionStorageService.getItem(curr);

      switch (curr) {
        case 'toAddress':
          return {
            ...acc,
            [curr]: `${_getAddress(value.customPart)}${_getAddress(value.postCode)}${_getAddress(value.city)}${_getAddress(
              value.country,
              true
            )}`,
            toLoadingDistance: loadingDistance[value.loadingDistance],
            toType: accommodationType[value.type],
            toElevator: elevator[value.elevator],
            toFloor: renderFloor(value.floor),
            toSize: `${value.size} m2`
          };

        case 'fromAddress':
          return {
            ...acc,
            [curr]: `${_getAddress(value.customPart)}${_getAddress(value.postCode)}${_getAddress(value.city)}${_getAddress(
              value.country,
              true
            )}`,
            fromLoadingDistance: loadingDistance[value.loadingDistance],
            fromType: accommodationType[value.type],
            fromElevator: elevator[value.elevator],
            fromFloor: renderFloor(value.floor),
            fromSize: `${value.size} m2`
          };

        case 'storageUnit':
          return {...acc, [curr]: value ? 'Yej' : 'Nej'};

        case 'furnitureAmount':
          return {...acc, [curr]: value ? furnitureAmount[value] : '-'};

        case 'extraServices':
          return {...acc, [curr]: [...(acc['extraServices'] || []), ...value.map(service => extraServices[service.extraServiceType])]};

        case 'cleaning':
          return value
            ? {...acc, ['extraServices']: [...(acc['extraServices'] || []), extraServices['Cleaning']]}
            : {...acc, ['extraServices']: [...(acc['extraServices'] || [])]};

        case 'jobDate':
          return {...acc, [curr]: value};

        case 'cleaningJobDate':
          return {...acc, [curr]: value};

        default:
          return {...acc, [curr]: value || '-'};
      }
    }, {});

    if (SessionStorageService.getItem('cleaningJobDate')) {
      Object.keys(data).forEach(item => {
        summaryContentPageWithCleaning = summaryContentPageWithCleaning.replace(`{${item}}`, data[item]);
      });
      summaryPage.innerHTML = summaryContentPageWithCleaning;
    } else {
      Object.keys(data).forEach(item => {
        summaryContentPage = summaryContentPage.replace(`{${item}}`, data[item]);
      });
      summaryPage.innerHTML = summaryContentPage;
    }

    if (!!data.extraServices.length) {
      const extraServicesRow = document.getElementById('extra-services');
      const extraServicesNode = `
      <section class="summary-section">
        <h3 class="summary-section__title">Ytterligare tjänster</h3>
        <div class="summary-section__divider"></div>
        ${data.extraServices
          .map(item => {
            return `<div class="summary-section__row_service">
          <div class="summary-section__value_service">${item.extraServiceName}</div>
          <div class="summary-section__description">${item.extraServiceDescription}</div>
        </div>`;
          })
          .join('')}
      </section>`;

      extraServicesRow.innerHTML = extraServicesNode;
    }
  }
}

function openSuccessTaskMessage() {
  const modal = document.getElementById('modal_success_task');
  const header = document.getElementById('header');
  if (modal) {
    document.body.style.overflow = 'hidden';
    header.style.zIndex = 1;
    modal.classList.remove('closed');
  }
}

function updateRequest(e) {
  const fieldsArr = [
    'name',
    'phone',
    'email',
    'taskId',
    'taskKey',
    'movingComment',
    'storageUnit',
    'jobDate',
    'extraServices',
    'cleaning',
    'fromAddress',
    'toAddress',
    'furnitureAmount',
    'cleaningJobDate',
    'cleaningComment'
  ];

  const sendRequestButton = document.getElementById('send-request-button');

  const _setLoadingInButtonsUI = buttons => {
    buttons.forEach(button => {
      button.disabled = true;
      button.classList.add('loading');
    });
  };

  const _removeLoadingInButtonsUI = buttons => {
    buttons.forEach(button => {
      button.disabled = false;
      button.classList.remove('loading');
    });
  };

  const data = fieldsArr.reduce((acc, curr) => {
    if (SessionStorageService.getItem(curr) !== null) {
      return {...acc, [curr]: SessionStorageService.getItem(curr)};
    }
    return {...acc};
  }, {});

  const onSuccess = () => {
    _removeLoadingInButtonsUI([sendRequestButton]);
    // openSuccessTaskMessage();
    window.location.href = `${window.location.origin}/tack-offert`;
    SessionStorageService.clear();
  };

  const onError = error => {
    alert(error);
    _removeLoadingInButtonsUI([sendRequestButton]);
  };

  _setLoadingInButtonsUI([e.currentTarget]);
  AjaxRequestService.sendPutAjaxRequest(data, onSuccess, onError, '/wp-json/fd-api/v1/task/create');
}

function goBack() {
  window.history.back();
}

function addEventListenerToClosePageButton() {
  const closePageButton = document.getElementById('modal_success_task_close_page');
  if (closePageButton) {
    closePageButton.addEventListener('click', function () {
      window.location.href = `${window.location.origin}`;
    });
  }
}
