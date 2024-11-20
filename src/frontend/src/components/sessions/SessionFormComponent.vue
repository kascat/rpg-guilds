<template>
  <q-card>
    <q-card-section>
      <q-form
        ref="itemFormRef"
        @submit="saveItem()"
        class="row q-col-gutter-md"
      >
        <q-input
          v-model="session.name"
          :label="t('name') + ' *'"
          dense
          outlined
          hide-bottom-space
          class="col-xs-8"
          :rules="[val => !!val || t('mandatory_completion')]"
        />
        <q-input
          v-model="configuration.players_per_guild"
          :label="t('players_per_guild') + ' *'"
          dense
          outlined
          hide-bottom-space
          type="number"
          step="1"
          min="1"
          class="col-xs-12 col-md-4"
          :rules="[val => val > 0 || t('mandatory_completion')]"
        />
        <q-select
          class="col-xs-12"
          :label="t('players') + ' *'"
          map-options
          emit-value
          clearable
          v-model="configuration.selected_players"
          :options="playerOptions"
          :option-label="opt => opt.name"
          dense
          outlined
          use-input
          fill-input
          multiple
          use-chips
          :placeholder="t('type_to_search')"
          input-debounce="300"
          @filter="filterPlayers"
          @update:model-value="filterSelectedPlayers()"
          :rules="[val => !!val || t('mandatory_completion')]"
        >
          <template v-slot:append>
            <q-icon
              name="o_info"
              class="cursor-pointer"
            />
            <q-tooltip :offset="[5, 5]">
              {{ t('select_players_info') }}
            </q-tooltip>
          </template>
        </q-select>

        <div v-if="configuration.selected_players.length" class="col-xs-12">
          <div class="q-mb-sm text-h6">
            {{ t('players_detail') }}
          </div>
          <q-card flat bordered class="bg-blue-grey-1">
            <q-card-section class="q-pa-none">
              <div
                @dragenter="onDragEnter"
                @dragleave="onDragLeave"
                @dragover="onDragOver"
                @drop="onDrop"
                :data-box="true"
                class="overflow-hidden row q-pa-sm"
              >
                <div
                  v-for="(player, index) in configuration.selected_players"
                  :key="`available-${index}`"
                  :id="`player-${player.id}`"
                  draggable="true"
                  class="col-xs-12 col-sm-4 col-md-3 q-pa-sm"
                  @dragstart="onDragStart"
                  :data-player="player.id"
                >
                  <q-item class="bg-primary rounded-borders cursor-pointer">
                    <q-item-section>
                      <q-item-label class="text-bold">{{ player.name }}</q-item-label>
                      <q-item-label caption lines="1">
                        {{ t('class') }}: {{ t(`player_classes.${player.class}`) }} <br>
                        {{ t('xp') }}: {{ player.xp }}
                      </q-item-label>
                    </q-item-section>
                  </q-item>
                </div>
              </div>
            </q-card-section>
          </q-card>
        </div>

        <div class="col-xs-12">
          <div class="row justify-between q-mb-md items-center">
            <div>
              <div class="q-mb-sm text-h6">
                {{ t('player_guilds') }}
              </div>
              <div class="text-grey-7">{{ t('drag_tip') }}</div>
            </div>
<!--            TODO: Habilitar adição personalisada -->
<!--            <q-btn-->
<!--              icon="add"-->
<!--              :label="t('add_guild')"-->
<!--              color="primary"-->
<!--              outline-->
<!--              @click="configuration.guilds.push([])"-->
<!--            />-->
            <q-btn
              icon="star"
              :label="t('organize')"
              color="primary"
              outline
              @click="organizeGuilds()"
            />
          </div>

          <div class="row">
            <q-banner
              v-if="!configuration.guilds.length"
              inline-actions
              rounded
              class="col-xs-12 bg-primary text-white"
            >
              {{ t('no_guilds') }}
            </q-banner>
            <div class="col-xs-12 row">
              <div
                v-for="(guild, guildIndex) in configuration.guilds"
                :key="`guild-${guildIndex}`"
                class="col-xs-12 col-sm-6 col-md-4 q-pa-xs"
              >
                <q-card
                  flat
                  bordered
                  class="bg-blue-grey-1"
                >
                  <div class="text-bold q-pa-md">
                    {{ t('player_on_guild') }}
                  </div>

                  <q-separator/>

                  <q-card-section>
                    <div
                      @dragenter="onDragEnter"
                      @dragleave="onDragLeave"
                      @dragover="onDragOver"
                      @drop="onDrop"
                      :data-box="true"
                      :data-guild="guildIndex"
                      class="overflow-hidden q-pa-s q-pb-xl full-height"
                    >
                      <div
                        v-for="(guildPlayer, guildPlayerIndex) in guild"
                        :key="`available-${guildPlayerIndex}`"
                        :id="`player-${guildPlayer.id}`"
                        draggable="true"
                        class="col-xs-12 col-sm-4 col-md-3 q-pa-sm"
                        @dragstart="onDragStart"
                        :data-player="guildPlayer.id"
                      >
                        <q-item class="bg-primary rounded-borders cursor-pointer">
                          <q-item-section>
                            <q-item-label class="text-bold">{{ guildPlayer.name }}</q-item-label>
                            <q-item-label caption lines="1">
                              {{ t('class') }}: {{ t(`player_classes.${guildPlayer.class}`) }} <br>
                              {{ t('xp') }}: {{ guildPlayer.xp }}
                            </q-item-label>
                          </q-item-section>
                        </q-item>
                      </div>
                    </div>
                  </q-card-section>
                </q-card>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xs-12 text-right">
          <q-btn
            outline
            :label="t('save')"
            icon="save"
            type="submit"
            color="primary"
            :disable="saving || !configuration.guilds.length"
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
import { createSession, getSession, updateSession } from 'src/services/sessions/sessions-api';
import { formatResponseError } from 'src/services/utils/error-formatter';
import { getPlayers } from 'src/services/players/players-api';

const router = useRouter();

const props = defineProps({
  formItemId: {
    required: true,
  },
});

const session = ref({
  name: null,
  guilds: [],
});

const configuration = ref({
  guilds: [],
  selected_players: [],
  players_per_guild: null,
});

const playerOptions = ref([]);
const saving = ref(false);
const itemFormRef = ref(null);
const organizing = ref(false);

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
      const dataToSave = { ...session.value };
      dataToSave.guilds = configuration.value.guilds.map((g) => {
        return {
          players: g.map((p) => p.id),
        };
      });

      if (!props.formItemId) {
        await createSession(dataToSave);
      } else {
        await updateSession(props.formItemId, dataToSave);
      }

      Notify.create({
        message: t('saved_successfully'),
        type: 'positive',
      });

      router.push({ name: 'sessions' });
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
    session.value = await getSession(id);
  } catch (e) {
    Notify.create({
      message: t('failed_to_load'),
      type: 'negative',
    });
  }
  Loading.hide();
}

async function organizeGuilds() {
  organizing.value = true;
  try {
    alert('111');
  } catch (e) {
    Notify.create({
      message: t('failed_to_organize'),
      type: 'negative',
    });
  }
  organizing.value = false;
}

async function filterPlayers(val, update, abort) {
  try {
    playerOptions.value = await getPlayers({
      active: true,
      name: val,
      rowsPerPage: 25,
    });
    update();
  } catch (e) {
    Notify.create({
      message: t('failed_to_load'),
      type: 'negative',
    });
    abort();
  }
}

function filterSelectedPlayers() {
  const selectedIds = configuration.value.selected_players.map(p => p.id);

  configuration.value.guilds = configuration.value.guilds.map((guild) => {
    return guild.filter(p => selectedIds.includes(p.id));
  });
}

// store the id of the draggable element
function onDragStart(e) {
  e.dataTransfer.setData('text', e.target.id);
  e.dataTransfer.dropEffect = 'move';
}

function onDragEnter(e) {
  // don't drop on other draggables
  if (e.target.draggable !== true && e.target.dataset.box === 'true') {
    e.target.classList.add('drag-enter');
  }
}

function onDragLeave(e) {
  e.target.classList.remove('drag-enter');
}

function onDragOver(e) {
  e.preventDefault();
}

function onDrop(e) {
  e.preventDefault();

  // don't drop on other draggables
  if (e.target.draggable === true || e.target.dataset.box !== 'true') {
    return;
  }

  const draggedId = e.dataTransfer.getData('text');
  const draggedEl = document.getElementById(draggedId);

  // check if original parent node
  if (draggedEl.parentNode === e.target) {
    e.target.classList.remove('drag-enter');
    return;
  }

  // make the exchange
  // draggedEl.parentNode.removeChild(draggedEl);
  // e.target.appendChild(draggedEl);
  e.target.classList.remove('drag-enter');

  const draggedPlayer = configuration.value.selected_players.find(p => p.id == draggedEl.dataset.player);

  configuration.value.guilds = configuration.value.guilds.map((guild) => {
    return guild.filter(p => p.id !== draggedPlayer.id);
  });

  configuration.value.guilds[+e.target.dataset.guild].push(draggedPlayer);
}
</script>

<style scoped lang="sass">
.drag-enter
  outline-style: dashed
</style>
