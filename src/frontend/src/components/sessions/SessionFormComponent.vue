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
          :label="t('session_name') + ' *'"
          dense
          outlined
          hide-bottom-space
          class="col-xs-12"
          :readonly="isShowMode"
          :rules="[val => !!val || t('mandatory_completion')]"
        />
        <q-select
          v-if="!isShowMode"
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
                :data-box="!isShowMode"
                class="overflow-hidden row q-pa-sm"
              >
                <div
                  v-for="(player, index) in configuration.selected_players"
                  :key="`available-${index}`"
                  :id="`player-${player.id}`"
                  :draggable="!isShowMode"
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
            <div class="col-xs-12 q-mb-sm text-h6">
              {{ t('player_guilds') }}
            </div>
            <div v-if="!isShowMode" class="col-xs-12 row q-col-gutter-md">
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
              />
              <q-select
                v-model="configuration.strategy"
                :label="t('strategy') + ' *'"
                :options="strategyOptions"
                option-label="label"
                option-value="value"
                map-options
                emit-value
                dense
                outlined
                hide-bottom-space
                class="col-xs-12 col-md-4"
              />
              <div class="col-xs-12 col-md-4 row items-center">
                <q-btn
                  class="q-ml-md full-width"
                  icon="star"
                  :label="t('organize')"
                  color="secondary"
                  outline
                  :disable="!configuration.players_per_guild || !configuration.strategy || organizing"
                  :loading="organizing"
                  @click="sortGuilds()"
                />
              </div>
            </div>
          </div>

          <div v-if="session.guilds.length && !isShowMode" class="q-my-md">
            <q-separator/>
            <div class="text-grey-7 q-mt-md text-center">{{ t('drag_tip') }}</div>
          </div>

          <div class="row">
            <q-banner
              v-if="!session.guilds.length"
              inline-actions
              rounded
              class="col-xs-12 bg-primary text-white"
            >
              {{ t('no_guilds') }}
            </q-banner>
            <div class="col-xs-12 row">
              <div
                v-for="(guild, guildIndex) in session.guilds"
                :key="`guild-${guildIndex}`"
                class="col-xs-12 col-sm-6 col-md-3 q-pa-xs"
              >
                <q-card
                  flat
                  bordered
                  class="bg-blue-grey-1"
                >
                  <div class="q-pa-md text-center">
                    <div class="text-bold text-uppercase">
                      {{ t('guild') }} {{ guildIndex + 1 }}
                    </div>
                    <div class="q-pb-s">
                      {{ t('xp') }}: {{ xpInGuild(guild.players) }}
                    </div>
                    <q-separator/>
                  </div>

                  <div class="q-px-md q-pb-sm">
                    <div class="text-bold">
                      {{ t('player_on_guild') }}:
                    </div>

                    <div
                      v-for="(playerClass, classIndex) in PLAYER_CLASSES"
                      :key="`class-${classIndex}`"
                    >
                      <q-chip
                        :color="chipColorToClassInGuild(guild.players, playerClass)"
                        text-color="white"
                        :icon="PLAYER_CLASS_ICONS[playerClass]"
                        class="full-width"
                      >
                        {{ t('player_classes.' + playerClass) }}:
                        {{ classQuantityInGuild(guild.players, playerClass) }}
                      </q-chip>
                    </div>

                    <div class="text-bold">
                      {{ t('player_on_guild') }}:
                    </div>
                  </div>

                  <q-separator/>

                  <q-card-section>
                    <div
                      @dragenter="onDragEnter"
                      @dragleave="onDragLeave"
                      @dragover="onDragOver"
                      @drop="onDrop"
                      :data-box="!isShowMode"
                      :data-guild="guildIndex"
                      class="overflow-hidden q-pa-s q-pb-lg full-height text-center"
                    >
                      <div
                        v-for="(guildPlayer, guildPlayerIndex) in (guild.players || [])"
                        :key="`available-${guildPlayerIndex}`"
                        :id="`player-${guildPlayer.id}`"
                        :draggable="!isShowMode"
                        class="col-xs-12 col-sm-4 col-md-3 q-pa-sm text-left"
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
                      {{ !isShowMode ? t('drag_and_drop_guild_info') : '' }}
                    </div>
                  </q-card-section>
                </q-card>
              </div>
            </div>
          </div>
        </div>

        <div v-if="!isShowMode" class="col-xs-12 text-right">
          <q-btn
            outline
            :label="t('save')"
            icon="save"
            type="submit"
            color="primary"
            :disable="saving || !session.guilds.length"
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
import { organizeGuilds } from 'src/services/guilds/guilds-api';
import { PLAYER_CLASS_ICONS, PLAYER_CLASSES } from 'src/constants/player_classes';
import { BALANCE_STRATEGY } from 'src/constants/balance_strategy';

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
  strategy: null,
});

const playerOptions = ref([]);
const saving = ref(false);
const itemFormRef = ref(null);
const organizing = ref(false);
const isShowMode = ref(false);

const strategyOptions = Object.values(BALANCE_STRATEGY).map((value) => ({
  label: t(`balance_strategy.${value}`),
  value: value,
}));

onMounted(async () => {
  if (props.formItemId) {
    isShowMode.value = true;
    await fetchItem(props.formItemId);
  }
});

async function saveItem() {
  if (!!props.formItemId) {
    return;
  }

  saving.value = true;
  try {
    const validated = await itemFormRef.value.validate();

    if (validated) {
      const dataToSave = { ...session.value };
      dataToSave.guilds = session.value.guilds.map((g) => {
        g.players = g.players.map((p) => p.id);
        return g;
      });

      await createSession(dataToSave);

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
    session.value = await getSession(id, { with: [ 'guilds.players' ] });
  } catch (e) {
    Notify.create({
      message: t('failed_to_load'),
      type: 'negative',
    });
  }
  Loading.hide();
}

async function sortGuilds() {
  organizing.value = true;
  try {
    session.value.guilds = await organizeGuilds({
      strategy: configuration.value.strategy,
      players_per_guild: configuration.value.players_per_guild,
      players: configuration.value.selected_players.map((p) => p.id),
    });
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

  session.value.guilds = session.value.guilds.map((guild) => {
    return {
      ...guild,
      players: guild.players.filter(p => selectedIds.includes(p.id)),
    };
  });
}

function xpInGuild(players) {
  return (players || []).reduce((previousValue, currentPlayer) => {
    return previousValue + (currentPlayer.xp || 0);
  }, 0);
}

function chipColorToClassInGuild(players, playerClass) {
  return (players || []).some((p) => p.class === playerClass) ? 'teal' : 'warning';
}

function classQuantityInGuild(players, playerClass) {
  return (players || []).filter((p) => p.class === playerClass).length;
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

  session.value.guilds = session.value.guilds.map((guild) => {
    return {
      ...guild,
      players: guild.players.filter(p => p.id !== draggedPlayer.id),
    };
  });

  session.value.guilds[+e.target.dataset.guild].players.push(draggedPlayer);
}
</script>

<style scoped lang="sass">
.drag-enter
  outline-style: dashed
</style>
