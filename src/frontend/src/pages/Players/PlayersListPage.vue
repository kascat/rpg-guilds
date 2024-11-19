<template>
  <div class="q-pa-md">
    <div class="row justify-between q-mb-md items-center">
      <h5 class="q-ma-none text-weight-regular">
        {{ t('players') }}
      </h5>
      <q-btn
        icon="add"
        :label="t('register')"
        color="primary"
        outline
        :to="{ name: 'players_create' }"
      />
    </div>

    <players-list/>
  </div>
</template>

<script setup>
import PlayersList from 'components/players/PlayersListComponent';
import { t } from 'src/services/utils/i18n';
</script>
