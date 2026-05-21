class FeedbackPage {
  complainModeButton = document.getElementById('complain-mode-button');
  pageTitle = document.getElementById('feedback-page-title');
  pageSubTitle = document.getElementById('feedback-page-subtitle');
  fileInputContainer = document.getElementById('file-input-container');
  pageFooter = document.getElementById('feedback-page-footer');
  attachmentInput = document.getElementById('feedback-page__attachment-input');
  attachmentContainer = document.getElementById('feedback-page__attachment-container');
  submitButton = document.getElementById('feedback-page__submit-button');
  homeButton = document.getElementById('feedback-page__home-button');
  successMessage = document.getElementById('feedback-page__success-message');
  feedBackForm = document.getElementById('feedback-page__feedback-form');

  constructor(feedbackPage) {
    this.page = feedbackPage;
    this.complainModeButton.addEventListener('click', this.switchToComplaintMode);
    this.attachmentInput.addEventListener('change', this.handleAttachFile);
    this.submitButton.addEventListener('click', this.sendFeedback);
    this.homeButton.addEventListener('click', this.goHome);
    this.switchToCleaningJobTypeMode();
  }

  goHome = () => {
    window.location.href = '/';
  };

  sendFeedback = async event => {
    const isComplaintMode = document.getElementById('feedback-page-title').innerText === 'Klagomål'; //Complaint
    const url = isComplaintMode ? '/wp-json/fd-api/v1/task/complain' : '/wp-json/fd-api/v1/task/feedback';
    const data = await this._getFormValues();
    const removeLoading = this._removeLoadingInButtonsUI.bind(this, [event.target]);

    if (data) {
      this._setLoadingInButtonsUI([event.target]);

      const onSuccess = () => {
        removeLoading([event.target]);
        this.feedBackForm.style.display = 'none';
        this.successMessage.style.display = 'block';
        this.page.scrollIntoView({behavior: 'smooth'});
      };

      const onError = error => {
        this._showError(error);
        removeLoading([event.target]);
      };

      AjaxRequestService.sendFeedBackRequest(data, onSuccess, onError, url);
    }
  };

  switchToCleaningJobTypeMode = () => {
    const urlParams = new URLSearchParams(window.location.search);
    const jobType = urlParams.get('jobType');
    const isCleaningJobType = jobType === 'Cleaning';

    if (isCleaningJobType) {
      this.pageTitle.innerHTML = 'Din städ beställning har slutförts.<br/> Hur nöjd är du?';
    }
  };

  switchToComplaintMode = () => {
    this.pageTitle.innerHTML = 'Klagomål'; //Complaint
    this.pageSubTitle.innerHTML = 'Klagomål'; //Complaint
    this.fileInputContainer.style.display = 'grid';
    this.pageFooter.style.display = 'none';
  };

  switchToFeedbackMode = () => {
    this.pageTitle.innerHTML = 'Din beställning är klar.<br/> Hur tyckte du om den?'; //'Your order has been complete.<br/> How did you like it?'
    this.pageSubTitle.innerHTML = 'Feedback';
    this.fileInputContainer.style.display = 'none';
    this.pageFooter.style.display = 'flex';
  };

  handleAttachFile = event => {
    const handleDeleteAttachedFile = this.handleDeleteAttachedFile.bind(this);

    let attachmentNodesArr = [];

    if (this.attachmentContainer.childNodes) {
      attachmentNodesArr = Array.from(this.attachmentContainer.childNodes);
    }
    const filesArr = Array.from(event.target.files) || [];

    if (attachmentNodesArr.length) {
      attachmentNodesArr.forEach(item => {
        if (!filesArr.some(file => `${file.name}${file.size}` === item.id)) {
          item.remove();
        }
      });
    }

    filesArr.forEach(file => {
      if (file) {
        const reader = new FileReader();

        reader.onload = function (e) {
          if (!attachmentNodesArr.some(node => node.id === `${file.name}${file.size}`)) {
            const attachmentContainer = document.getElementById('feedback-page__attachment-container');
            const container = document.createElement('div');
            container.classList.add('input-file__attachment-preview__container');
            container.id = `${file.name}${file.size}`;

            const image = new Image();
            image.src = e.target.result;
            image.classList.add('input-file__attachment-preview__image');

            const closeIcon = document.createElement('div');
            closeIcon.classList.add('input-file__attachment-preview__close-icon');
            closeIcon.onclick = handleDeleteAttachedFile(`${file.name}${file.size}`);

            container.appendChild(image);
            container.appendChild(closeIcon);
            attachmentContainer.appendChild(container);
          }
        };
        reader.readAsDataURL(file);
      }
    });
  };

  handleDeleteAttachedFile = id => () => {
    const filesArr = Array.from(this.attachmentInput.files);
    const filteredFilesArr = filesArr.filter(file => id !== `${file.name}${file.size}`);

    const dataTransfer = new DataTransfer();

    filteredFilesArr.forEach(function (file) {
      dataTransfer.items.add(file);
    });

    this.attachmentInput.files = dataTransfer.files;

    const changeEvent = new Event('change');
    this.attachmentInput.dispatchEvent(changeEvent);
  };

  _getFormValues = async () => {
    const inputs = Array.from(this.page.getElementsByTagName('input'));
    const textarea = Array.from(this.page.getElementsByTagName('textarea'));

    const isCommentValid = ValidationService.validateStringLength(textarea[0].value, 0, 10000);

    if (!isCommentValid) {
      this._showError('För många tecken. Vänligen förkorta din text');
      return;
    }

    const inputValues = inputs.reduce((acc, curr) => {
      if (curr.type === 'checkbox') {
        return {...acc, [curr.name]: curr.checked ? [...(acc[curr.name] || []), curr.value] : [...(acc[curr.name] || [])]};
      }
      if (curr.type === 'radio') {
        return curr.checked ? {...acc, [curr.name]: curr.value} : {...acc};
      }
      if (curr.type === 'file') {
        return curr.files ? {...acc, [curr.name]: curr.files} : {...acc};
      }

      return curr.value ? {...acc, [curr.name]: curr.value} : {...acc};
    }, {});

    const textareaValues = textarea.reduce((acc, curr) => {
      return {...acc, [curr.name]: curr.value};
    }, {});

    const values = {...inputValues, ...textareaValues};

    let requestData = {...values};

    const isComplaintMode = document.getElementById('feedback-page-title').innerText === 'Klagomål'; //Complaint;
    const urlParams = new URLSearchParams(window.location.search);
    const taskKey = urlParams.get('taskKey');
    const jobId = urlParams.get('jobId');

    if (isComplaintMode) {
      let files = {};
      if (values.files && !!values.files.length) {
        const filesArr = Array.from(values.files);
        const filesObjBase64 = filesArr.map(async file => {
          return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = e => resolve(reader?.result?.split(',')[1]);
            reader.onerror = error => reject(error);
            reader.readAsDataURL(file);
          });
        });

        const filesBase64Arr = await Promise.allSettled([...filesObjBase64]);
        const filesBase64Obj = filesArr.reduce((acc, curr, index) => {
          if (filesBase64Arr[index].value) {
            return {
              ...acc,
              [curr.name]: filesBase64Arr[index].value
            };
          }
          return {...acc};
        }, {});
        files = filesBase64Obj;
      }

      requestData = {
        taskKey,
        jobId,
        priceFeedback: values.PriceFeedback,
        customerServiceFeedback: values.CustomerServiceFeedback,
        jobServiceFeedback: values.JobServiceFeedback,
        complaint: values.comment.trim(),
        files
      };
    } else {
      requestData = {
        taskKey,
        jobId,
        priceFeedback: values.PriceFeedback,
        customerServiceFeedback: values.CustomerServiceFeedback,
        jobServiceFeedback: values.JobServiceFeedback,
        feedbackText: values.comment.trim()
      };
    }

    return requestData;
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

  _showError(error) {
    alert(error);
  }
}
