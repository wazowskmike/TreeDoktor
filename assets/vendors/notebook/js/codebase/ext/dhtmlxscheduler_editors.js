/*
 dhtmlxScheduler v.4.1.0 Stardard

 This software is covered by GPL license. You also can obtain Commercial or Enterprise license to use it in non-GPL project - please contact sales@dhtmlx.com. Usage without proper license is prohibited.

 (c) Dinamenta, UAB.
 */
scheduler.form_blocks.combo = {render: function (e) {
    e.cached_options || (e.cached_options = {});
    var t = "";
    return t += "<div class='" + e.type + "' style='height:" + (e.height || 20) + "px;' ></div>"
}, set_value: function (e, t, i, s) {
    !function () {
        function t() {
            e._combo && e._combo.DOMParent && e._combo.destructor()
        }

        t();
        var i = scheduler.attachEvent("onAfterLightbox", function () {
            t(), scheduler.detachEvent(i)
        })
    }(), window.dhx_globalImgPath = s.image_path || "/", e._combo = new dhtmlXCombo(e, s.name, e.offsetWidth - 8), s.onchange && e._combo.attachEvent("onChange", s.onchange), s.options_height && e._combo.setOptionHeight(s.options_height);
    var n = e._combo;
    if (n.enableFilteringMode(s.filtering, s.script_path || null, !!s.cache), s.script_path) {
        var a = i[s.map_to];
        a ? s.cached_options[a] ? (n.addOption(a, s.cached_options[a]), n.disable(1), n.selectOption(0), n.disable(0)) : dhtmlxAjax.get(s.script_path + "?id=" + a + "&uid=" + scheduler.uid(), function (e) {
            var t = e.doXPath("//option")[0], i = t.childNodes[0].nodeValue;
            s.cached_options[a] = i, n.addOption(a, i), n.disable(1), n.selectOption(0), n.disable(0)
        }) : n.setComboValue("")
    } else {
        for (var r = [], d = 0; d < s.options.length; d++) {
            var o = s.options[d], l = [o.key, o.label, o.css];
            r.push(l)
        }
        if (n.addOption(r), i[s.map_to]) {
            var h = n.getIndexByValue(i[s.map_to]);
            n.selectOption(h)
        }
    }
}, get_value: function (e, t, i) {
    var s = e._combo.getSelectedValue();
    return i.script_path && (i.cached_options[s] = e._combo.getSelectedText()), s
}, focus: function () {
}}, scheduler.form_blocks.radio = {render: function (e) {
    var t = "";
    t += "<div class='dhx_cal_ltext dhx_cal_radio' style='height:" + e.height + "px;' >";
    for (var i = 0; i < e.options.length; i++) {
        var s = scheduler.uid();
        t += "<input id='" + s + "' type='radio' name='" + e.name + "' value='" + e.options[i].key + "'><label for='" + s + "'> " + e.options[i].label + "</label>", e.vertical && (t += "<br/>")
    }
    return t += "</div>"
}, set_value: function (e, t, i, s) {
    for (var n = e.getElementsByTagName("input"), a = 0; a < n.length; a++) {
        n[a].checked = !1;
        var r = i[s.map_to] || t;
        n[a].value == r && (n[a].checked = !0)
    }
}, get_value: function (e) {
    for (var t = e.getElementsByTagName("input"), i = 0; i < t.length; i++)if (t[i].checked)return t[i].value
}, focus: function () {
}}, scheduler.form_blocks.checkbox = {render: function (e) {
    return scheduler.config.wide_form ? '<div class="dhx_cal_wide_checkbox" ' + (e.height ? "style='height:" + e.height + "px;'" : "") + "></div>" : ""
}, set_value: function (e, t, i, s) {
    e = document.getElementById(s.id);
    var n = scheduler.uid(), a = "undefined" != typeof s.checked_value ? t == s.checked_value : !!t;
    e.className += " dhx_cal_checkbox";
    var r = "<input id='" + n + "' type='checkbox' value='true' name='" + s.name + "'" + (a ? "checked='true'" : "") + "'>", d = "<label for='" + n + "'>" + (scheduler.locale.labels["section_" + s.name] || s.name) + "</label>";
    if (scheduler.config.wide_form ? (e.innerHTML = d, e.nextSibling.innerHTML = r) : e.innerHTML = r + d, s.handler) {
        var o = e.getElementsByTagName("input")[0];
        o.onclick = s.handler
    }
}, get_value: function (e, t, i) {
    e = document.getElementById(i.id);
    var s = e.getElementsByTagName("input")[0];
    return s || (s = e.nextSibling.getElementsByTagName("input")[0]), s.checked ? i.checked_value || !0 : i.unchecked_value || !1
}, focus: function () {
}};
//# sourceMappingURL=../sources/ext/dhtmlxscheduler_editors.js.map