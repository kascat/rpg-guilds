<template>
  <div class="q-pa-md">
    <div class="row justify-between q-mb-md items-center">
      <h5 class="q-ma-none text-weight-regular">
        {{ t('sessions') }}
      </h5>
      <q-btn
        icon="add"
        :label="t('create_session')"
        color="primary"
        outline
        :to="{ name: 'sessions_create' }"
      />
    </div>

    <sessions-list/>
  </div>
</template>

<script setup>
import SessionsList from 'components/sessions/SessionsListComponent';
import { t } from 'src/services/utils/i18n';
</script>
