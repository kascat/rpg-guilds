
<template>
  <div class="login-page row items-center justify-center">
    <div class="col-xs-12 col-sm-7 col-md-5 col-lg-4">
      <div class="row items-center justify-center">
        <div class="image-container text-center">
          <img
            src="~assets/logo.png"
            alt="Logo image"
            style="max-height:90px; max-width: 80vw"
          />
          <div class="text-h5">
            {{ t('app_name') }}
          </div>
        </div>
      </div>
      <login-form
        v-if="!showForgotPassword"
        @show-forgot-password="showForgotPassword = true"
      />
      <forgot-password-form
        v-if="showForgotPassword"
        @hide-forgot-password="showForgotPassword = false"
      />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import LoginForm from 'components/auth/LoginForm';
import ForgotPasswordForm from 'components/auth/ForgotPasswordForm';
import { t } from 'src/services/utils/i18n';

let showForgotPassword = ref(false);
</script>
<style scoped>
.login-page {
  min-height: 100vh;
  min-width: 100vw;
  padding: 20px 20px 5px 20px;
  background: radial-gradient(#3b677c 0%, #171348 100%);
  display: flex;
}

.image-container {
  background: #cbc7fa;
  border-radius: 15px;
  margin-bottom: 15px;
  padding: 10px 10px 5px;
  z-index: 2;
}
</style>
