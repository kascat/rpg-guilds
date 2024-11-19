<template>
  <q-card>
    <q-card-section>
      <q-form
        ref="itemFormRef"
        @submit="saveItem()"
        class="row q-col-gutter-md"
      >
        <q-input
          v-model="player.name"
          :label="t('name') + ' *'"
          dense
          outlined
          hide-bottom-space
          class="col-xs-12 col-md-5"
          :rules="[val => !!val || t('mandatory_completion')]"
        />
        <q-select
          v-model="player.class"
          :label="t('class') + ' *'"
          :options="classesOptions"
          option-label="label"
          option-value="value"
          map-options
          emit-value
          dense
          outlined
          hide-bottom-space
          class="col-xs-12 col-md-4"
          :rules="[val => !!val || t('mandatory_completion')]"
        />
        <q-input
          v-model="player.xp"
          :label="t('xp') + ' *'"
          dense
          outlined
          hide-bottom-space
          type="number"
          step="1"
          min="1"
          max="100"
          class="col-xs-12 col-md-3"
          :rules="[val => val > 0 || t('mandatory_completion')]"
        />
        <div class="col-xs-12 text-right">
          <q-checkbox
            v-model="player.active"
            :label="t('active')"
            class="q-mr-xl"
          />
          <q-btn
            outline
            :label="t('save')"
            icon="save"
            type="submit"
            color="primary"
            :disable="saving"
            :loading="saving"
          />
        </div>
      </q-form>
    </q-card-section>
  </q-card>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { Loading, Notify } from 'quasar';
import { useRouter } from 'vue-router';
import { t } from 'src/services/utils/i18n';
import { createPlayer, getPlayer, updatePlayer } from 'src/services/players/players-api';
import { formatResponseError } from 'src/services/utils/error-formatter';
import { PLAYER_CLASSES } from 'src/constants/player_classes';

const router = useRouter();

const props = defineProps({
  formItemId: {
    required: true,
  },
});

const player = ref({
  name: null,
  class: null,
  xp: null,
  active: true,
});

const saving = ref(false);
const itemFormRef = ref(null);

const classesOptions = Object.values(PLAYER_CLASSES).map((value) => ({
  label: t(`player_classes.${value}`),
  value: value,
}));

onMounted(async () => {
  if (props.formItemId) {
    await fetchItem(props.formItemId);
  }
});

async function saveItem() {
  saving.value = true;
  try {
    const validated = await itemFormRef.value.validate();

    if (validated) {
      if (!props.formItemId) {
        await createPlayer(player.value);
      } else {
        await updatePlayer(props.formItemId, player.value);
      }

      Notify.create({
        message: t('saved_successfully'),
        type: 'positive',
      });

      router.push({ name: 'players' });
    }
  } catch (error) {
    Notify.create({
      message: formatResponseError(error) || t('failed_to_save'),
      type: 'negative',
    });
  }
  saving.value = false;
}

async function fetchItem(id) {
  Loading.show();
  try {
    player.value = await getPlayer(id);
  } catch (e) {
    Notify.create({
      message: t('failed_to_load'),
      type: 'negative',
    });
  }
  Loading.hide();
}
</script>
