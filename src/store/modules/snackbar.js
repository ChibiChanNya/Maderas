const state = () => ({
  snack: '',
  color: '',
});

const mutations = {
  setSnack (state, {text, color}) {
    state.snack = text;
    state.color = color ? color : "error";
  }
};

export default {
  state,
  mutations,
}