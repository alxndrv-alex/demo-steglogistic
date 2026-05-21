class AjaxRequestService {
  sendPostAjaxRequest = (data, s, e, apiUrl) => {
    const onSuccess = s || function () {};
    const onError = e || function () {};

    jQuery.ajax({
      url: `${window.location.origin}${apiUrl}`,
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify(data),
      success: function (data) {
        if (!data.success) {
          onError(`Error: ${data.error}`);
        } else {
          const {task_key, task_id} = data;
          onSuccess({taskKey: task_key, taskId: task_id});
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
          Array.isArray(jqXHR.responseJSON.message)
            ? onError(`Error: ${jqXHR.responseJSON.message[0]}`)
            : onError(`Error: ${jqXHR.responseJSON.message}`);
        } else {
          onError(`Unknown error occurred`);
        }
      }
    });
  };

  sendPutAjaxRequest = (data, s, e, apiUrl) => {
    const onSuccess = s || function () {};
    const onError = e || function () {};

    jQuery.ajax({
      url: `${window.location.origin}${apiUrl}`,
      method: 'PUT',
      contentType: 'application/json',
      data: JSON.stringify(data),
      success: function (data) {
        if (!data.success) {
          onError(`Error: ${data.error}`);
        } else {
          onSuccess(data);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
          Array.isArray(jqXHR.responseJSON.message)
            ? onError(`Error: ${jqXHR.responseJSON.message[0]}`)
            : onError(`Error: ${jqXHR.responseJSON.message}`);
        } else {
          onError(`Unknown error occurred`);
        }
      }
    });
  };

  static sendPostAjaxRequest = (data, s, e, apiUrl) => {
    const onSuccess = s || function () {};
    const onError = e || function () {};
    jQuery.ajax({
      url: `${window.location.origin}${apiUrl}`,
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify(data),
      success: function (data) {
        const {success, task_key, task_id, paymentUrl} = data;
        if (!success) {
          onError(`Error: ${data.error}`);
        } else {
          onSuccess({taskKey: task_key, taskId: task_id, paymentUrl});
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
          Array.isArray(jqXHR.responseJSON.message)
            ? onError(`Error: ${jqXHR.responseJSON.message[0]}`)
            : onError(`Error: ${jqXHR.responseJSON.message}`);
        } else if (jqXHR.responseJSON.error) {
          onError(jqXHR.responseJSON.error);
        } else {
          onError(`Unknown error occurred`);
        }
      }
    });
  };

  static sendPutAjaxRequest = (data, s, e, apiUrl) => {
    const onSuccess = s || function () {};
    const onError = e || function () {};

    jQuery.ajax({
      url: `${window.location.origin}${apiUrl}`,
      method: 'PUT',
      contentType: 'application/json',
      data: JSON.stringify(data),
      success: function (data) {
        const {success} = data;
        if (success === 'fail') {
          onError(`Error: ${data.error}`);
        } else {
          onSuccess(data);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
          Array.isArray(jqXHR.responseJSON.message)
            ? onError(`Error: ${jqXHR.responseJSON.message[0]}`)
            : onError(`Error: ${jqXHR.responseJSON.message}`);
        } else {
          onError(`Unknown error occurred`);
        }
      }
    });
  };

  static sendFeedBackRequest = (data, s, e, apiUrl) => {
    const onSuccess = s || function () {};
    const onError = e || function () {};

    if (data.Files) {
      let files = [];
      const filePromises = [];
      Array.from(data.Files).forEach(file => {
        const reader = new FileReader();
        const promise = new Promise((resolve, reject) => {
          reader.onload = function (e) {
            const regex = /^data:image\/\w+;base64,/;
            const result = e.target.result.replace(regex, '');
            files.push({[file.name]: result});
            resolve();
          };
          reader.onerror = function (error) {
            reject(error);
          };
        });

        filePromises.push(promise);
        reader.readAsDataURL(file);
      });

      Promise.all(filePromises)
        .then(() => {
          data.Files = files;
          jQuery.ajax({
            url: `${window.location.origin}${apiUrl}`,
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function (data) {
              const {success, task_key, task_id} = data;
              if (!success) {
                onError(`Error: ${data.error}`);
              } else {
                onSuccess({taskKey: task_key, taskId: task_id});
              }
            },
            error: function (jqXHR, textStatus, errorThrown) {
              if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                Array.isArray(jqXHR.responseJSON.message)
                  ? onError(`Error: ${jqXHR.responseJSON.message[0]}`)
                  : onError(`Error: ${jqXHR.responseJSON.message}`);
              } else {
                onError(`Unknown error occurred`);
              }
            }
          });
        })
        .catch(error => {
          alert('Error reading files:', error);
        });
    } else {
      jQuery.ajax({
        url: `${window.location.origin}${apiUrl}`,
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: function (data) {
          const {success, task_key, task_id} = data;
          if (!success) {
            onError(`Error: ${data.error}`);
          } else {
            onSuccess({taskKey: task_key, taskId: task_id});
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          if (jqXHR.responseJSON && jqXHR.responseJSON.error) {
            Array.isArray(jqXHR.responseJSON.message)
              ? onError(`Error: ${jqXHR.responseJSON.error[0]}`)
              : onError(`Error: ${jqXHR.responseJSON.error}`);
          } else {
            onError(`Unknown error occurred`);
          }
        }
      });
    }
  };

  static sendPostRequest = (data, s, e, apiUrl) => {
    const onSuccess = s || function () {};
    const onError = e || function () {};
    jQuery.ajax({
      url: `${window.location.origin}${apiUrl}`,
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify(data),
      success: function (data) {
        const {success} = data;
        if (!success) {
          onError(`Error: ${data.error}`);
        } else {
          onSuccess(data);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
          Array.isArray(jqXHR.responseJSON.message)
            ? onError(`Error: ${jqXHR.responseJSON.message[0]}`)
            : onError(`Error: ${jqXHR.responseJSON.message}`);
        } else {
          onError(`Unknown error occurred`);
        }
      }
    });
  };
}
