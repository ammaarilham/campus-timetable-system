window.alert = function (message, timeout = null, callback = null) {
  const alert = document.createElement("div");
  const alertMessage = document.createElement("span"); // Create alert message element
  const alertButtonsContainer = document.createElement("div");

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
          z-index: 9999; // Increase z-index to ensure it's above other elements
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
          justify-content: center;
          margin-top: 10px;
          `
  );

  alertMessage.textContent = message; // Set the alert message content
  alert.appendChild(alertMessage); // Append the message to the alert div
  alert.appendChild(alertButtonsContainer);

  alertButtonsContainer.innerText = "OK"; // Set button text

  alertButtonsContainer.addEventListener("click", (e) => {
    alert.remove();
    if (callback) {
      callback(false);
    }
  });

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
