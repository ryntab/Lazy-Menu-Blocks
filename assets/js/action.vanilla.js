document.addEventListener("DOMContentLoaded", function(){
    let hasBlock = document.getElementsByClassName('menu-item-has-block');
    for (var i = 0; i < hasBlock.length; i++) {
        hasBlock.item(i).onmouseenter = function() {
            if (this.classList.contains('loaded')) return;
            this.blockID = this.children[0].getAttribute('data-block');
            console.log(this.blockID);
            let item = this;
            fetcher(item);
        }
    }

    const fetcher = (item) => {
        fetch(`${window.location.origin}/wp-json/lazyMenu/UX/block/${item.blockID}`)
            .then(response => response.json())
            .then(data => {
                item.classList.add('loaded');
                item.querySelectorAll('.sub-menu')[0].innerHTML = data.block;
        });
     }
});
