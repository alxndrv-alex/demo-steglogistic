class RequestForm {
  activeStep = 1;
  taskKey = sessionStorage.getItem('taskKey') || null;
  taskId = sessionStorage.getItem('taskId') || null;
  form = document.getElementById('request-form');
  shortForm = document.getElementById('request-form_short');
  formSteps = Array.from(document.getElementsByClassName('request-form__step'));
  nextButtons = Array.from(document.getElementsByClassName('request-form__next-button'));
  backButtons = Array.from(document.getElementsByClassName('request-form__back-button'));
  createShortRequestButtons = Array.from(document.getElementById('request-form_short').getElementsByTagName('button'));
  createLongRequestButton = document.getElementById('request-form').getElementsByClassName('request-form__send-button')[0];
  cleaningInput = document.getElementById('cleaning');
  cleaningButtonFalse = document.getElementById('cleaning__false');
  cleaningButtonTrue = document.getElementById('cleaning__true');
  inputName = document.getElementById('request-form__input-name');
  inputEmail = document.getElementById('request-form__input-email');
  inputPhone = document.getElementById('request-form__input-phone');
  inputTermsOfServiceAgreed = document.getElementById('first');
  movingJobDate = document.getElementById('movingJobDate');
  cleaningForm = document.getElementById('cleaningForm');
  cleaningDateInput = document.getElementById('cleaningJobDate');
  cleaningCommentInput = document.getElementById('cleaningJobComment');
  activatedCleaningFormButtons = document.getElementById('activatedCleaningFormButtons');
  deactivateCleaningButton = document.getElementById('deactivateCleaningButton');
  requestError = '';
  toPostCodeInput = document.getElementById('toPostCode');
  fromPostCodeInput = document.getElementById('fromPostCode');
  cleaningNextButton = document.getElementById('cleaning__next-button');
  movingCommentInput = document.getElementById('movingJobComment');

  constructor(eventEmitter) {
    this.eventEmitter = eventEmitter;
    this.inputPhone.addEventListener('input', this.autocompletePhone);
    this.inputTermsOfServiceAgreed.addEventListener('click', () => this._removeErrorFromUI([this.inputTermsOfServiceAgreed]));
    this.toPostCodeInput.addEventListener('input', this.autocompletePostCode);
    this.fromPostCodeInput.addEventListener('input', this.autocompletePostCode);

    //remove error styles when focusing
    Array.from(this.shortForm.getElementsByTagName('input')).forEach(input =>
      input.addEventListener('focus', () => this._removeErrorFromUI([input]))
    );
    Array.from(this.form.getElementsByTagName('input')).forEach(input =>
      input.addEventListener('focus', () => this._removeErrorFromUI([input]))
    );
    Array.from(this.form.getElementsByTagName('select')).forEach(input =>
      input.addEventListener('focus', () => this._removeErrorFromUI([input]))
    );
    Array.from(this.form.getElementsByTagName('textarea')).forEach(input =>
      input.addEventListener('focus', () => this._removeErrorFromUI([input]))
    );
    //end region

    if (!SessionStorageService.getItem('name')) {
      this.form.reset();
    } else {
      this.eventEmitter.emit('openModal');
      this.movingJobDate.value = SessionStorageService.getItem('movingJobDate');
      this._restoreFormValuesFromSession();
    }

    this.ajaxRequestService = new AjaxRequestService();
    this.eventEmitter.on('closeModal', this.resetForm);
    this.createLongRequestButton.addEventListener('click', this.openSummary);
    this.createShortRequestButtons.forEach(button => {
      button.addEventListener('click', this.createRequest);
    });

    this.nextButtons.forEach(button => {
      button.addEventListener('click', this.goToTheNextStep);
    });

    this.backButtons.forEach(button => {
      button.addEventListener('click', this.goToThePreviousStep);
    });

    this.cleaningButtonFalse.addEventListener('click', () => {
      this.setCleaningInput(false);
      this.deactivateCleaningForm();
    });

    this.cleaningButtonTrue.addEventListener('click', () => {
      this.setCleaningInput(true);
      this.activateCleaningForm();
      this.cleaningNextButton.style.display = 'block';
    });

    this.deactivateCleaningButton.addEventListener('click', () => {
      this.setCleaningInput(false);
      this.deactivateCleaningForm();
      this.cleaningNextButton.style.display = 'none';
      this.goToTheNextStep();
    });
  }

  goToTheNextStep = () => {
    const isValid = this._validateCurrentFormStepValues();
    const isLastStep = this.activeStep === 4;

    if (!isValid) {
      return;
    }
    if (!isLastStep) {
      this._setActiveStep(this.activeStep + 1);
    }
  };

  goToThePreviousStep = () => {
    const isFirstStep = this.activeStep === 1;
    if (!isFirstStep) {
      this._setActiveStep(this.activeStep - 1);
    }
  };

  resetForm = () => {
    this._setActiveStep(1);
    this.form.reset();
    SessionStorageService.clear();
  };

  createRequest = event => {
    const createTaskUrl = '/wp-json/fd-api/v1/task/create';
    const isValid = this._validateShortFormValues();
    if (isValid) {
      this._setLoadingInButtonsUI([event.currentTarget]);
      const data = this._getShortFormValues();
      const removeLoading = this._removeLoadingInButtonsUI.bind(this, [event.currentTarget]);
      const onSuccess = ({taskKey, taskId}) => {
        this._setTaskKey(taskKey);
        this._setTaskId(taskId);
        this.eventEmitter.emit('openModal');
        this.shortForm.reset();
        removeLoading([event.currentTarget]);
        Object.keys(data).forEach(key => {
          SessionStorageService.setItem([key], data[key]);
        });
      };

      const onError = error => {
        this._showError(error);
        this._setError(error);
        removeLoading([event.currentTarget]);
      };

      //TODO: uncomment after handling long form
      this.ajaxRequestService.sendPostAjaxRequest(data, onSuccess, onError, createTaskUrl);
    }
  };

  openSummary = () => {
    const data = this._getLongFormValues();

    console.log('openSummary', data);

    Object.keys(data).forEach(key => {
      if (
        (key === 'taskId' && SessionStorageService.getItem('taskId')) ||
        (key === 'taskKey' && SessionStorageService.getItem('taskKey'))
      ) {
        return;
      }
      SessionStorageService.setItem(key, data[key]);
    });

    history.pushState(null, null, `${window.location.origin}/sammanfattning`);
    window.location.href = `${window.location.origin}/sammanfattning`;
  };

  setCleaningInput(value) {
    this.cleaningInput.checked = value;
  }

  activateCleaningForm() {
    this.cleaningForm.style.display = 'flex';
    this.cleaningDateInput.required = true;
    this.activatedCleaningFormButtons.style.display = 'none';
    this.deactivateCleaningButton.style.display = 'block';
  }

  deactivateCleaningForm() {
    this._cleanCleaningForm();
    this.cleaningForm.style.display = 'none';
    this.cleaningDateInput.required = false;
    this.activatedCleaningFormButtons.style.display = 'flex';
    this.activatedCleaningFormButtons.style.gap = '8px';
    this.deactivateCleaningButton.style.display = 'none';
  }

  _cleanCleaningForm() {
    this.cleaningDateInput.value = '';
    this.cleaningCommentInput.value = '';
    SessionStorageService.setItem('cleaningJobDate', null);
    SessionStorageService.setItem('cleaningComment', null);
    SessionStorageService.setItem('cleaning', false);
  }

  _showError(error) {
    alert(error);
  }

  _setErrorInUI = inputs => {
    inputs.forEach(input => {
      input.classList.add('error');
    });

    setTimeout(() => {
      inputs.forEach(input => {
        input.classList.remove('error');
      });
    }, 60000);
  };

  _removeErrorFromUI = inputs => {
    inputs.forEach(input => {
      input.classList.remove('error');
    });
  };

  _renderErrorText = (nodes, message) => {
    nodes.forEach(node => {
      node.innerHTML = message;
      node.classList.remove('invisible');
    });

    setTimeout(() => {
      nodes.forEach(node => {
        // node.innerHTML = '';
        node.classList.add('invisible');
      });
    }, 4000);
  };

  _setLoadingInButtonsUI = buttons => {
    buttons.forEach(button => {
      button.disabled = true;
      button.classList.add('loading');
    });
  };

  _removeLoadingInButtonsUI = buttons => {
    buttons.forEach(button => {
      button.disabled = false;
      button.classList.remove('loading');
    });
  };

  _setTaskKey = taskKey => {
    this.taskKey = taskKey;
    sessionStorage.setItem('taskKey', taskKey);
  };

  _setTaskId = taskId => {
    this.taskId = taskId;
    sessionStorage.setItem('taskId', taskId);
  };

  _setError = error => {
    this.requestError = error;
  };

  _getShortFormValues = () => {
    const inputs = Array.from(this.shortForm.getElementsByTagName('input'));
    const formValues = inputs.reduce((acc, curr) => {
      return {...acc, [curr.name]: curr.type === 'checkbox' ? curr.checked : curr.value};
    }, {});
    return formValues;
  };

  _getLongFormValues = () => {
    const inputs = Array.from(this.form.getElementsByTagName('input'));
    const selects = Array.from(this.form.getElementsByTagName('select'));
    const textarea = Array.from(this.form.getElementsByTagName('textarea'));

    const inputValues = inputs.reduce((acc, curr) => {
      if (curr.type === 'checkbox') {
        return {...acc, [curr.name]: curr.checked ? [...(acc[curr.name] || []), curr.value] : [...(acc[curr.name] || [])]};
      }
      if (curr.type === 'radio') {
        return curr.checked ? {...acc, [curr.name]: curr.value} : {...acc};
      }
      return curr.value ? {...acc, [curr.name]: curr.value} : {...acc};
    }, {});

    const selectValues = selects.reduce((acc, curr) => {
      return {...acc, [curr.name]: curr.options[curr.selectedIndex].value};
    }, {});

    const textareaValues = textarea.reduce((acc, curr) => {
      return {...acc, [curr.name]: curr.value};
    }, {});

    const values = {...inputValues, ...selectValues, ...textareaValues};

    let data = {
      taskId: this.taskId,
      taskKey: this.taskKey,
      furnitureAmount: values.furnitureAmount,
      movingComment: values.movingComment || null,
      storageUnit: values.storageUnit === 'yes',
      jobDate: values.jobDate,
      dayPart: 'None',
      extraServices: values.additionalService.map(service => ({extraServiceType: service})),
      cleaning: values.cleaning === 'on',
      cleaningJobDate: values.cleaningJobDate || null,
      cleaningComment: values.cleaningComment || null,
      fromAddress: {
        //start address
        country: 'Sweden',
        customPart: values.fromCustomAddress,
        city: values.fromCity,
        state: '',
        postCode: values.fromPostCode,
        //end address
        size: values.fromSize,
        floor: values.fromFloor,
        elevator: values.fromElevator,
        loadingDistance: values.fromLoadingDistance,
        type: values.fromAccommodationType
      },
      toAddress: {
        //start address
        country: 'Sweden',
        customPart: values.toCustomAddress,
        city: values.toCity,
        state: '',
        postCode: values.toPostCode,
        //end address
        size: values.toSize,
        floor: values.toFloor,
        elevator: values.toElevator,
        loadingDistance: values.toLoadingDistance,
        type: values.toAccommodationType
      }
    };

    Object.keys(data).forEach(key => {
      if (data[key] === undefined) {
        delete data[key];
      }
    });

    return data;
  };

  _validateShortFormValues = () => {
    const inputs = Array.from(this.shortForm.getElementsByTagName('input'));
    const requiredInputs = inputs.filter(input => input.required);
    const isRequiredValid = requiredInputs.every(input => (input.type === 'checkbox' ? input.checked : !!input.value.trim()));

    if (!isRequiredValid) {
      this._setErrorInUI(requiredInputs.filter(input => (input.type === 'checkbox' ? !input.checked : !input.value.trim())));
    }

    const isNameValid = ValidationService.validateFullName(this.inputName.value);
    if (!isNameValid) {
      this._setErrorInUI([this.inputName]);
    }

    const isEmailValid = ValidationService.validateEmail(this.inputEmail.value);
    if (!isEmailValid) {
      this._setErrorInUI([this.inputEmail]);
    }

    const isPhoneValid = ValidationService.validatePhoneNumber(this.inputPhone.value);
    if (!isPhoneValid) {
      this._setErrorInUI([this.inputPhone]);
    }

    return isRequiredValid && isEmailValid && isPhoneValid && isNameValid;
  };

  _getActiveStepFormValues = () => {
    const step = this.formSteps.find(step => step.classList.contains(`request-form__step-${this.activeStep}`));
    const inputs = Array.from(step.getElementsByTagName('input'));
    const selects = Array.from(step.getElementsByTagName('select'));
    return {inputs, selects};
  };

  _validateCurrentFormStepValues = () => {
    //validate requiring

    const {inputs, selects} = this._getActiveStepFormValues();
    const requiredInputs = inputs.filter(input => input.required);
    const requiredSelects = selects.filter(select => select.required);
    const isInputsFilled = requiredInputs.every(input => !!input.value.trim());
    const isSelectsFilled = requiredSelects.every(item => !!item.selectedIndex);
    const isFilled = isInputsFilled && isSelectsFilled;
    const sizeField = inputs.find(input => input.name === 'toSize' || input.name === 'fromSize');
    const customAddressField = inputs.find(input => input.name === 'fromCustomAddress' || input.name === 'toCustomAddress');
    const postcodeField = inputs.find(input => input.name === 'fromPostCode' || input.name === 'toPostCode');
    let isNumbersFieldsValid = true;
    let isCustomAddressValid = true;
    let isPostCodeValid = true;
    if (isFilled && postcodeField) {
      isPostCodeValid = ValidationService.validatePostcode(postcodeField.value);
      if (!isPostCodeValid) {
        this._setErrorInUI([postcodeField]);
      }
    }

    if (isFilled && sizeField) {
      isNumbersFieldsValid = ValidationService.validateNumber(sizeField.value, 1, 9999);
      if (!isNumbersFieldsValid) {
        this._setErrorInUI([sizeField]);
      }
    }

    if (isFilled && customAddressField) {
      isCustomAddressValid = ValidationService.validateAddress(customAddressField.value);
      if (!isCustomAddressValid) {
        this._setErrorInUI([customAddressField]);
      }
    }

    if (!isFilled) {
      this._setErrorInUI(requiredInputs.filter(input => !input.value.trim()));
      this._setErrorInUI(requiredSelects.filter(input => !input.selectedIndex));
    }

    return isFilled && isNumbersFieldsValid && isCustomAddressValid && isPostCodeValid;
  };

  _setActiveStep = stepNumber => {
    this.formSteps.forEach(step => {
      if (step.classList.contains(`request-form__step-${stepNumber}`)) {
        step.classList.add('active');
      } else {
        step.classList.remove('active');
      }
    });
    this.activeStep = stepNumber;
  };

  autocompletePhone = event => {
    let inputValue;
    inputValue = event.target.value.replace(/\D/g, '');
    event.target.value = inputValue;
  };

  autocompletePostCode = event => {
    let inputValue;
    inputValue = event.target.value.replace(/\s/g, '');
    event.target.value = inputValue;
  };

  _restoreFormValuesFromSession = () => {
    const get = k => SessionStorageService.getItem(k);

    this.movingJobDate && (this.movingJobDate.value = get('jobDate') || '');

    if (this.inputTermsOfServiceAgreed) {
      const agreed = get('termsOfServiceAgreed');
      if (typeof agreed === 'boolean') this.inputTermsOfServiceAgreed.checked = agreed;
    }

    if (this.movingCommentInput) this.movingCommentInput.value = get('movingComment') || '';

    const fillAddress = (prefix, obj) => {
      if (!obj) return;
      const setVal = (name, val) => {
        const el = this.form.querySelector(`[name="${name}"]`);
        if (!el) return;
        if (el.tagName === 'SELECT') {
          Array.from(el.options).forEach(o => {
            o.selected = o.value == String(val);
          });
        } else {
          el.value = val ?? '';
        }
      };

      setVal(`${prefix}CustomAddress`, obj.customPart ?? '');
      setVal(`${prefix}City`, obj.city ?? '');
      setVal(`${prefix}PostCode`, obj.postCode ?? '');
      setVal(`${prefix}Size`, obj.size ?? '');
      setVal(`${prefix}Floor`, obj.floor ?? '');
      setVal(`${prefix}Elevator`, obj.elevator ?? '');
      setVal(`${prefix}LoadingDistance`, obj.loadingDistance ?? '');
      setVal(`${prefix}AccommodationType`, obj.type ?? '');
    };

    fillAddress('from', get('fromAddress'));
    fillAddress('to', get('toAddress'));

    const furnitureAmount = get('furnitureAmount');
    if (furnitureAmount) {
      const el = this.form.querySelector(`input[type="radio"][name="furnitureAmount"][value="${CSS.escape(furnitureAmount)}"]`);
      if (el) el.checked = true;
    }

    const storageUnit = get('storageUnit');
    if (typeof storageUnit === 'boolean') {
      const v = storageUnit ? 'yes' : 'no';
      const el = this.form.querySelector(`input[type="radio"][name="storageUnit"][value="${v}"]`);
      if (el) el.checked = true;
    }

    const extra = get('extraServices');
    if (Array.isArray(extra)) {
      const types = extra.map(x => x?.extraServiceType).filter(Boolean);
      this.form.querySelectorAll(`input[type="checkbox"][name="additionalService"]`).forEach(cb => {
        cb.checked = types.includes(cb.value);
      });
    } else {
      const arr = Array.isArray(extra) ? extra : [];
      this.form.querySelectorAll(`input[type="checkbox"][name="additionalService"]`).forEach(cb => {
        cb.checked = arr.includes(cb.value);
      });
    }

    const isCleaning = !!get('cleaning'); // boolean
    if (this.cleaningInput) {
      this.cleaningInput.checked = isCleaning;
    }
    if (isCleaning) {
      this.activateCleaningForm?.();
      if (this.cleaningDateInput) this.cleaningDateInput.value = get('cleaningJobDate') || '';
      if (this.cleaningCommentInput) this.cleaningCommentInput.value = get('cleaningComment') || '';
      if (this.cleaningNextButton) this.cleaningNextButton.style.display = 'block';
    } else {
      this.deactivateCleaningForm?.();
      if (this.cleaningDateInput && get('cleaningJobDate')) this.cleaningDateInput.value = get('cleaningJobDate');
      if (this.cleaningCommentInput && get('cleaningComment')) this.cleaningCommentInput.value = get('cleaningComment');
    }
  };

  _bindPageShowRestore = () => {
    window.addEventListener('pageshow', e => {
      const nav = performance.getEntriesByType && performance.getEntriesByType('navigation')[0];
      const isBack = e.persisted || (nav && nav.type === 'back_forward');
      if (isBack) this._restoreFormValuesFromSession();
    });
  };
}
