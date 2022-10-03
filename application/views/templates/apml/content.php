<style>
    #images_container{
        /*display: grid;
        grid-template-columns: 1fr;
        grid-template-rows: 1fr;*/
        border: 1px solid red;
        position: relative;
    }

    #images_container img {
        position: absolute;
    }
</style>


<div id="testApp">
    <h1>{{ ratio }}</h1>
    <input type="range" name="range" id="" v-model="size" min="640" max="1300">
    <div id="images_container" v-bind:style="`width: ` + size + `px; height: ` + bgHeight + `px;`">
        <img v-for="image in images"
            v-bind:style="`top: ` + (image.top * ratio) + `px; left: ` + (image.left * ratio) + `px; width:` + imgWidth + `px;`"
            v-bind:src="`<?= URL_IMG ?>icons/` + image.filename"
            alt="Imagen">
    </div>
</div>

<script>
const bgHeightOrg = 662;
const imgWidthOrg = 100;

var testApp = new Vue({
    el: '#testApp',
    created: function(){
        //this.get_list()
    },
    data: {
        size: 800,
        loading: false,
        images: [
            {filename: 'a.png', top: 0, left: 0},
            {filename: 'b.png', top: 20, left: 20},
            {filename: 'c.png', top: 40, left: 300},
            {filename: 'd.png', top: 150, left: 400},
        ]
    },
    methods: {
        
    },
    computed: {
        ratio: function(){
            return this.size / 800
        },
        bgHeight: function(){
            return this.ratio * bgHeightOrg
        },
        imgWidth: function(){
            return this.ratio * imgWidthOrg
        },
    },
})
</script>