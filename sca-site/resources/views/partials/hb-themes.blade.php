<div class="hb-swatches" role="group" aria-label="Choisir le fond">
    <template x-for="t in themes" :key="t.id">
        <button type="button" class="hb-swatch" :title="t.label" :aria-label="'Fond ' + t.label"
                :aria-pressed="theme === t.id" :class="theme === t.id && 'is-active'"
                :style="'background:' + t.swatch" @click="theme = t.id"></button>
    </template>
</div>
