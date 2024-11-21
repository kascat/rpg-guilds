import { get } from 'boot/axios';

export const organizeGuilds = async (params) => {
  const { data } = await get('organize-guilds', params);
  return data;
};

