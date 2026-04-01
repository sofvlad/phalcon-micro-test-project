<script>
export default {
  name: "ConfirmButton",
  props: {
    action: {
      type: Function,
      required: true
    },
    buttonText: {
      type: String,
      default: 'Действие'
    },
    type: {
      type: String,
      default: 'button'
    },
    requireConfirm: {
      type: Boolean,
      default: true
    },
    modalTitle: {
      type: String,
      default: 'Подтверждение действия'
    },
    modalMessage: {
      type: String,
      default: 'Вы уверены, что хотите выполнить это действие?'
    },
    confirmText: {
      type: String,
      default: 'Подтвердить'
    },
    cancelText: {
      type: String,
      default: 'Отмена'
    },
    confirmingText: {
      type: String,
      default: 'Выполнение...'
    },
    warningText: {
      type: String,
      default: 'Это действие нельзя отменить.'
    },
    variant: {
      type: String,
      default: 'primary',
      validator: (value) => [
        'primary',
        'secondary',
        'success',
        'danger',
        'warning',
        'info',
        'dark',
        'light'
      ].includes(value)
    },
    size: {
      type: String,
      default: 'md',
      validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    buttonAdditionalClass: {
      type: String,
      default: '',
    },
    modalSize: {
      type: String,
      default: 'md',
      validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
    },
    danger: {
      type: Boolean,
      default: false
    },
    outline: {
      type: Boolean,
      default: false
    },
    block: {
      type: Boolean,
      default: false
    },
    icon: {
      type: String,
      default: null
    },
    showSpinner: {
      type: Boolean,
      default: true
    },
    showSpinnerInConfirm: {
      type: Boolean,
      default: true
    },
    showCloseButton: {
      type: Boolean,
      default: true
    },
    disabled: {
      type: Boolean,
      default: false
    },
    autoClose: {
      type: Boolean,
      default: true
    },
    onSuccess: {
      type: Function,
      default: null
    },
    onError: {
      type: Function,
      default: null
    }
  },
  emits: ['confirmed', 'cancelled', 'success', 'error'],
  data() {
    return {
      showModal: false,
      isConfirming: false
    };
  },
  computed: {
    buttonClasses() {
      const classes = ['confirm-button btn'];

      if (this.outline) {
        classes.push(`btn-outline-${this.variant}`);
      } else {
        classes.push(`btn-${this.variant}`);
      }

      if (this.size !== 'md') {
        classes.push(`btn-${this.size}`);
      }

      if (this.block) {
        classes.push('w-100');
      }

      classes.push(...this.buttonAdditionalClass.split(' '));

      return classes;
    },

    confirmButtonClasses() {
      const classes = ['btn'];

      if (this.danger) {
        classes.push('btn-danger');
      } else if (this.outline) {
        classes.push(`btn-outline-${this.variant}`);
      } else {
        classes.push(`btn-${this.variant}`);
      }

      return classes;
    },

    modalSizeClass() {
      if (this.modalSize === 'sm') return 'modal-sm';
      if (this.modalSize === 'lg') return 'modal-lg';
      if (this.modalSize === 'xl') return 'modal-xl';
      return '';
    },

    modalHeaderClass() {
      if (this.danger) {
        return 'bg-danger text-white'
      }

      return '';
    }
  },
  methods: {
    handleClick() {
      if (this.requireConfirm) {
        this.openModal();
      } else {
        this.executeAction();
      }
    },
    openModal() {
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
      this.$emit('cancelled');
    },
    async confirmAction() {
      this.isConfirming = true;

      try {
        await this.executeAction();

        if (this.autoClose) {
          this.closeModal();
        }

        this.$emit('confirmed');

        if (this.onSuccess) {
          this.onSuccess();
        }
      } catch (error) {
        console.error('Action failed:', error);
        this.$emit('error', error);

        if (this.onError) {
          this.onError(error);
        }
      } finally {
        this.isConfirming = false;
      }
    },
    async executeAction() {
      try {
        const result = await this.action();
        this.$emit('success', result);
        return result;
      } catch (error) {
        this.$emit('error', error);
        throw error;
      }
    }
  }
}
</script>

<template>
  <button
      :type="type"
      :class="buttonClasses"
      :disabled="disabled || isConfirming"
      @click="handleClick"
  >
    <span v-if="isConfirming && showSpinner" class="spinner-border spinner-border-sm me-2"></span>
    {{ isConfirming ? confirmingText : buttonText }}
  </button>
  <div v-if="showModal" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5); z-index: 1050;">
    <div class="modal-dialog" :class="modalSizeClass">
      <div class="modal-content">
        <div class="modal-header" :class="modalHeaderClass">
          <h5 class="modal-title">
            <i v-if="icon" :class="icon"></i>
            {{ modalTitle }}
          </h5>
          <button
              v-if="showCloseButton"
              type="button"
              class="btn-close"
              :class="{ 'btn-close-white': danger }"
              @click="closeModal"
          ></button>
        </div>
        <div class="modal-body">
          <slot name="body">
            <p>{{ modalMessage }}</p>
            <p v-if="danger" class="text-danger mb-0">
              <i class="bi bi-exclamation-triangle"></i>
              {{ warningText }}
            </p>
          </slot>
        </div>
        <div class="modal-footer">
          <button
              type="button"
              class="btn btn-secondary"
              :disabled="isConfirming"
              @click="closeModal"
          >
            {{ cancelText }}
          </button>
          <button
              type="button"
              :class="confirmButtonClasses"
              :disabled="isConfirming"
              @click="confirmAction"
          >
            <span v-if="isConfirming && showSpinnerInConfirm" class="spinner-border spinner-border-sm me-2"></span>
            {{ isConfirming ? confirmingText : confirmText }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  overflow-x: hidden;
  overflow-y: auto;
  outline: 0;
}

.modal-dialog {
  position: relative;
  width: auto;
  margin: 1.75rem auto;
  pointer-events: none;
}

.modal-content {
  position: relative;
  display: flex;
  flex-direction: column;
  width: 100%;
  pointer-events: auto;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid rgba(0,0,0,.2);
  border-radius: 0.3rem;
  outline: 0;
}

.modal-header {
  display: flex;
  flex-shrink: 0;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1rem;
  border-bottom: 1px solid #dee2e6;
  border-top-left-radius: calc(0.3rem - 1px);
  border-top-right-radius: calc(0.3rem - 1px);
}

.modal-body {
  position: relative;
  flex: 1 1 auto;
  padding: 1rem;
}

.modal-footer {
  display: flex;
  flex-wrap: wrap;
  flex-shrink: 0;
  align-items: center;
  justify-content: flex-end;
  padding: 0.75rem;
  border-top: 1px solid #dee2e6;
  border-bottom-right-radius: calc(0.3rem - 1px);
  border-bottom-left-radius: calc(0.3rem - 1px);
}

.modal-footer > * {
  margin: 0.25rem;
}
</style>
