class Framr {
    constructor(id,caption,url,zix=999) {
        this.id = id;
        this.caption = caption;
        this.zindex = zix;
        this.full = false;
        this.fill = false;
        this.width = 600;
        this.height = 400;
        this.gutter = 30;
        this.url = url;
        this.state = 'closed';
    }
    set(pro,val) {
        this[pro] = val;
        return this;
    }
    render(replace=false) {
        let dom = document.getElementById('main_'+this.id);
        if (replace) {
            document.getElementById('greybox_'+this.id).outerHTML = '';
            dom.outerHTML = '';
            dom = null;
        }
        if (dom == null) {
            /*
            let html = `<div id='wrap_${this.id}'><div class='greybox' id='greybox_${this.id}' style='z-index:${this.zindex};'></div>
            <div class='frmain blue' id='main_${this.id}' style='z-index:${this.zindex};'>
                <div class='frcap'><span>${this.caption}</span><span onclick="document.getElementById('wrap_${this.id}').outerHTML='';"><i class="times circle icon"></i></span></div>
                <iframe src="${this.url}"></iframe>
            </div></div>`;
            document.body.innerHTML += html;
            */
            let wrap = document.createElement("div");
            wrap.id = 'wrap_'+this.id;
            let greybox = document.createElement("div");
            greybox.id = 'greybox_'+this.id;
            greybox.className = "greybox";
            greybox.style.zIndex = this.zindex;
            let main = document.createElement("div");
            main.id = "main_"+this.id;
            main.className = "frmain blue";
            main.style.zIndex = this.zindex;
            main.innerHTML = `
                <div class='frcap'><span>${this.caption}</span><span onclick="document.getElementById('wrap_${this.id}').outerHTML='';"><i class="times circle icon"></i></span></div>
                <iframe src="${this.url}"></iframe>
            `;
            wrap.appendChild(greybox);
            wrap.appendChild(main);
            document.body.appendChild(wrap);
        }
        this.show();
    }
    show() {
        let gb = document.getElementById('greybox_'+this.id);
        gb.style.left = 0;
        gb.style.top = 0;
        gb.style.right = 0;
        gb.style.bottom = 0;

        let dom = document.getElementById('main_'+this.id);
        let resize = true;
        let ww = Math.ceil(window.innerWidth);
        let wh = Math.ceil(window.innerHeight);
        if (this.full) {
            dom.style.left = 0;
            dom.style.top = 0;
            dom.style.right = 0;
            dom.style.bottom = 0;
            resize = false;
        }
        if (this.fill) {
            dom.style.left = this.gutter+"px";
            dom.style.top = this.gutter+"px";
            dom.style.right = this.gutter+"px";
            dom.style.bottom = this.gutter+"px";
            //dom.style.width = (ww-(2*this.gutter))+"px";
            //dom.style.bottom = (wh-(2*this.gutter))+"px";
            resize = false;
        }
        if (resize) {
            dom.style.width = this.width+"px";
            dom.style.height= this.height+"px";
            dom.style.left = ((ww-this.width)/2)+"px";
            dom.style.top = ((wh-this.height)/2)+"px";
        }
        //$('.dropdown').dropdown();
    }
}
