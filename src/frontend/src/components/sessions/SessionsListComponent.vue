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
        </div>
      </div>
    </template>
    <template v-slot:body-cell-actions="props">
      <q-td key="actions" :props="props">
        <q-btn-group outline>
          <q-btn
            outline
            color="primary"
            icon="visibility"
            :to="{ name: 'sessions_update', params: { 'id': props.row.id } }"
          >
            <q-tooltip>
              {{ t('view') }}
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
import { onMounted, ref } from 'vue';
import { getSessions, destroySession } from 'src/services/sessions/sessions-api';
import { t } from 'src/services/utils/i18n';
import { Notify, Dialog } from 'quasar';
import { formatDatetimeBR } from 'src/services/utils/date';

const listData = ref([]);
const loading = ref(false);
const removingId = ref(null);

const mainPagination = ref({
  page: 1,
  rowsPerPage: 10,
  rowsNumber: 0,
  with: ['guilds'],
});

const columns = [
  {
    name: 'name',
    label: t('name'),
    align: 'left',
    field: 'name',
    format: val => val || t('ni'),
  },
  {
    name: 'created_at',
    label: t('date'),
    align: 'left',
    field: 'created_at',
    format: val => val ? formatDatetimeBR(val) : t('ni'),
  },
  {
    name: 'guilds',
    label: t('guilds'),
    align: 'left',
    field: 'guilds',
    format: val => val?.length || '0'
  },
  {
    name: 'actions',
    align: 'center',
    label: t('actions'),
    field: 'id',
    sortable: false,
  },
];

onMounted(async () => {
  await fetchList();
});

async function fetchList(props) {
  loading.value = true;
  try {
    mainPagination.value = props?.pagination || mainPagination.value;
    listData.value = await getSessions(mainPagination.value);
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
      await destroySession(id);
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
