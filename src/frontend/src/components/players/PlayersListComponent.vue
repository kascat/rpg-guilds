<template>
  <q-table
    :rows="listData"
    :columns="columns"
    row-key="id"
    v-model:pagination="mainPagination"
    :loading="loading"
    :loading-label="t('loading')"
    :no-results-label="t('no_results')"
    :no-data-label="t('no_results')"
    binary-state-sort
    @request="fetchList"
  >
    <template v-slot:top>
      <div class="full-width">
        <div class="row q-col-gutter-md q-pt-xs">
          <q-input
            v-model="mainPagination.name"
            :label="t('name')"
            class="col-xs-12 col-md-4"
            outlined
            dense
            debounce="500"
            @update:model-value="fetchList()"
          />
          <q-checkbox
            v-model="mainPagination.active"
            :label="activeCheckboxLabel(mainPagination.active)"
            class="col-xs-12 col-md-4"
            debounce="500"
            toggle-indeterminate
            :true-value="true"
            :false-value="null"
            :indeterminate-value="false"
            indeterminate-icon="disabled_by_default"
            checked-icon="check_box"
            unchecked-icon="list_alt"
            @update:model-value="fetchList()"
          />
        </div>
      </div>
    </template>
    <template v-slot:body-cell-actions="props">
      <q-td key="actions" :props="props">
        <q-btn-group outline>
          <q-btn
            outline
            color="primary"
            icon="edit"
            :to="{ name: 'players_update', params: { 'id': props.row.id } }"
          >
            <q-tooltip>
              {{ t('update') }}
            </q-tooltip>
          </q-btn>
          <q-btn
            outline
            color="negative"
            icon="delete"
            :loading="removingId === props.row.id"
            :disable="removingId === props.row.id"
            @click="deleteItem(props.row.id)"
          >
            <q-tooltip>
              {{ t('remove') }}
            </q-tooltip>
          </q-btn>
        </q-btn-group>
      </q-td>
    </template>
  </q-table>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { getPlayers, destroyPlayer } from 'src/services/players/players-api';
import { t } from 'src/services/utils/i18n';
import { Notify, Dialog } from 'quasar';
import { checkIfLoggedUserHasAbility } from 'boot/user';
import { ABILITIES } from 'src/constants/abilities';

const listData = ref([]);
const loading = ref(false);
const removingId = ref(null);
const canManagePlayers = ref(false);

const mainPagination = ref({
  page: 1,
  rowsPerPage: 10,
  rowsNumber: 0,
});

const columns = computed(() => {
  const defaultColumns = [
    {
      name: 'name',
      label: t('name'),
      align: 'left',
      field: 'name',
      format: val => val || t('ni'),
    },
    {
      name: 'class',
      label: t('class'),
      align: 'left',
      field: 'class',
      format: val => val ? t(`player_classes.${val}`) : t('ni'),
    },
    {
      name: 'xp',
      label: t('xp'),
      align: 'left',
      field: 'xp',
    },
    {
      name: 'active',
      label: t('status'),
      align: 'left',
      field: 'active',
      format: val => t(val ? 'active' : 'inactive'),
    },
  ];

  if (canManagePlayers.value) {
    defaultColumns.push({
      name: 'actions',
      align: 'center',
      label: t('actions'),
      field: 'id',
      sortable: false,
    });
  }

  return defaultColumns;
});

onMounted(async () => {
  canManagePlayers.value = checkIfLoggedUserHasAbility(ABILITIES.MANAGE_PlayerS);

  await fetchList();
});

async function fetchList(props) {
  loading.value = true;
  try {
    mainPagination.value = props?.pagination || mainPagination.value;
    listData.value = await getPlayers(mainPagination.value);
  } catch (e) {
    Notify.create({
      message: t('failed_to_load_data'),
      type: 'negative',
    });
  }
  loading.value = false;
}

function deleteItem(id) {
  Dialog.create({
    title: t('warning'),
    message: t('confirm_remove'),
    cancel: true,
  }).onOk(async () => {
    removingId.value = id;
    try {
      await destroyPlayer(id);
      fetchList();

      Notify.create({
        message: t('removed_successfully'),
        type: 'positive',
      });
    } catch (e) {
      Notify.create({
        message: t('failed_to_remove'),
        type: 'negative',
      });
    }
    removingId.value = null;
  });
}

function activeCheckboxLabel(active) {
  if (true === active) {
    return t('active');
  }

  if (false === active) {
    return t('inactive');
  }

  return t('all');
}
</script>
