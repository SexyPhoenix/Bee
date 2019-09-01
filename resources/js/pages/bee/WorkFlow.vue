<template>
  <component v-bind:is="item.action_name" :item="item"></component>
</template>
<script>
import Deay from '../../components/bee/workflow/Deay'
import Ayqk from '../../components/bee/workflow/Ayqk'
import Etay from '../../components/bee/workflow/Etay'
import Wtay from '../../components/bee/workflow/Wtay'
import Dtat from '../../components/bee/workflow/Dtat'
import Adat from '../../components/bee/workflow/Adat'
import Anat from '../../components/bee/workflow/Anat'
import Alfh from '../../components/bee/workflow/Alfh'
import Clay from '../../components/bee/workflow/Clay'

export default {
  name: 'WorkFlow',
  props: {
    item: Object
  },
  components: {
    Deay,
    Ayqk,
    Etay,
    Wtay,
    Dtat,
    Adat,
    Anat,
    Alfh,
    Clay
  },
}
</script>
<style>
</style>