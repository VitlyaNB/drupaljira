class ConfirmDialog {
  constructor({ questionText, trueButtonText, falseButtonText, parent }) {
    this.questionText = questionText || 'Are you sure?';
    this.trueButtonText = trueButtonText || 'Yes';
    this.falseButtonText = falseButtonText || 'No';
    this.parent = parent || document.body;

    this.dialog = undefined;
    this.trueButton = undefined;
    this.falseButton = undefined;

    this._createDialog();
    this._appendDialog();
  }

  confirm() {
    return new Promise((resolve, reject) => {
      const somethingWentWrongUponCreation =
        !this.dialog || !this.trueButton || !this.falseButton;
      if (somethingWentWrongUponCreation) {
        reject(new Error('Something went wrong when creating the modal'));
        return;
      }

      this.dialog.showModal();
      this.trueButton.focus();

      this.trueButton.addEventListener('click', () => {
        resolve(true);
        this._destroy();
      });

      this.falseButton.addEventListener('click', () => {
        resolve(false);
        this._destroy();
      });
    });
  }

  _createDialog() {
    this.dialog = document.createElement('dialog');
    this.dialog.classList.add('fe-confirm-dialog');

    const question = document.createElement('div');
    question.textContent = this.questionText;
    question.classList.add('fe-confirm-dialog-question');
    this.dialog.appendChild(question);

    const buttonGroup = document.createElement('div');
    buttonGroup.classList.add('fe-confirm-dialog-button-group');
    this.dialog.appendChild(buttonGroup);

    this.falseButton = document.createElement('button');
    this.falseButton.classList.add(
      'fe-confirm-dialog-button',
      'fe-confirm-dialog-button--false',
    );
    this.falseButton.type = 'button';
    this.falseButton.textContent = this.falseButtonText;
    buttonGroup.appendChild(this.falseButton);

    this.trueButton = document.createElement('button');
    this.trueButton.classList.add(
      'fe-confirm-dialog-button',
      'fe-confirm-dialog-button--true',
    );
    this.trueButton.type = 'button';
    this.trueButton.textContent = this.trueButtonText;
    buttonGroup.appendChild(this.trueButton);
  }

  _appendDialog() {
    this.parent.appendChild(this.dialog);
  }

  _destroy() {
    this.parent.removeChild(this.dialog);
    delete this;
  }
}
