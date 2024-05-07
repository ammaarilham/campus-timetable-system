window.alert = function (message, timeout = null, callback = null) {
  const alert = document.createElement("div");
  const alertMessage = document.createElement("span");
  const alertButtonsContainer = document.createElement("div");
  const proceedButton = document.createElement("button");
  const cancelButton = document.createElement("button");

  alert.classList.add("alert");
  alert.setAttribute(
    "style",
    `
      position: fixed;
      top: 20px;
      left: 50%;
      padding: 20px;
      border-radius: 5px;
      box-shadow: 0 10px 5px 0 #00000022;
      display: flex;
      flex-direction: column;
      box-shadow: 2px 2px 4px rgba(0,0,0,.2);
      transform: translateX(-50%);
      background: white;
      z-index: 9999;
      `
  );

  alertMessage.setAttribute(
    "style",
    `
      padding: 10px;
      `
  );

  alertButtonsContainer.setAttribute(
    "style",
    `
      display: flex;
      justify-content: space-evenly;
      margin-top: 10px;
      `
  );

  proceedButton.innerText = "Proceed";
  proceedButton.setAttribute(
    "style",
    `
    border:none;
      background: #4CAF50;
      color: white;
      border-radius: 2px;
      padding: 5px 8px;
      cursor: pointer;
      `
  );
  proceedButton.addEventListener("click", () => {
    alert.remove();
    if (callback) {
      callback(true);
    }
  });

  cancelButton.innerText = "Cancel";
  cancelButton.setAttribute(
    "style",
    `
      border:none;
      background: #f44336;
      color: white;
      padding: 5px 8px;
      border-radius: 2px;

      cursor: pointer;
      `
  );
  cancelButton.addEventListener("click", () => {
    alert.remove();
    if (callback) {
      callback(false);
    }
  });

  alertMessage.textContent = message;
  alertButtonsContainer.appendChild(proceedButton);
  alertButtonsContainer.appendChild(cancelButton);
  alert.appendChild(alertMessage);
  alert.appendChild(alertButtonsContainer);

  document.body.appendChild(alert);

  if (timeout != null) {
    setTimeout(() => {
      alert.remove();
      if (callback) {
        callback(false);
      }
    }, Number(timeout));
  }
};
