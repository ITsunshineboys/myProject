//重置节点个数
setGearTooth: function(data) {
    var _self = this;
    var item = data || [];
    var l = item.length;
    if (_self.gearArea==null) { return false;}
    var gearChild = _self.gearArea.querySelectorAll(".gear");
    var gearVal = gearChild[_self.index].getAttribute('val');
    var maxVal = l - 1;
    if (gearVal > maxVal) {
        gearVal = maxVal;
    }
    gearChild[_self.index].setAttribute('data-len', l);
    if (l > 0) {
        var id = item[gearVal][this.keys['id']];
        var childData;
        switch (_self.type) {
            case 1:
                childData = item[gearVal].child
                break;
            case 2:
                var nextData= _self.data[_self.index+1]
                for (var i in nextData) {
                    if(i==id){
                        childData = nextData[i];
                        break;
                    }
                };
                break;
        }
        var itemStr = "";
        for (var i = 0; i < l; i++) {
            itemStr += "<div class='tooth' ref='" + item[i][this.keys['id']] + "'>" + item[i][this.keys['name']] + "";
        }
        gearChild[_self.index].innerHTML = itemStr;
        gearChild[_self.index].style["-webkit-transform"] = 'translate3d(0,' + (-gearVal * 2) + 'em,0)';
        gearChild[_self.index].setAttribute('top', -gearVal * 2 + 'em');
        gearChild[_self.index].setAttribute('val', gearVal);
        _self.index++;
        if (_self.index > 2) {
            _self.index = 0;
            return;
        }
        _self.setGearTooth(childData);
    } else {
        gearChild[_self.index].innerHTML = "";
        gearChild[_self.index].setAttribute('val', 0);
        if(_self.index==1){
            gearChild[2].innerHTML = '';
            gearChild[2].setAttribute('val', 0);
        }
        _self.index = 0;
    }
}
// 加入这行代码可以解决 滚动没有结束就点击取消报错
if (_self.gearArea==null) { return false;}
function gearTouchEnd(e) {
    e.preventDefault();
    var target = e.target;
    while (true) {
        if (!target.classList.contains("gear")) {
            target = target.parentElement;
        } else {
            break;
        }
    }
    var flag = (target["new_" + target.id] - target["old_" + target.id]) / (target["n_t_" + target.id] - target["o_t_" + target.id]);
    flag = isNaN(flag) ? 0.001 : flag;
    if (Math.abs(flag) <= 0.2) {
        target["spd_" + target.id] = (flag < 0 ? -0.08 : 0.08);
    } else {
        if (Math.abs(flag) <= 0.5) {
            target["spd_" + target.id] = (flag < 0 ? -0.16 : 0.16);
        } else {
            target["spd_" + target.id] = flag / 2;
        }
    }
    if (!target["pos_" + target.id]) {
        target["pos_" + target.id] = 0;
    }
    rollGear(target);
}