var bridge = function (presenterPath) {
    this.entries = [];
    this.controlSpawnSettings = [];

    window.rhubarb.viewBridgeClasses.JqueryViewBridge.apply(this, arguments);
};

bridge.prototype = new window.rhubarb.viewBridgeClasses.JqueryViewBridge();
bridge.prototype.constructor = bridge;

bridge.prototype.attachEvents = function () {
    this.updateDom();
};

bridge.prototype.onStateLoaded = function () {
    this.controlSpawnSettings = this.model.PresenterSpawnSettings;
};

bridge.prototype.wouldRequireNewEntry = function (entry) {
    return false;
};

bridge.prototype.updateDom = function () {
    var needsNewEntry = true;

    if (this.entries.length > 0) {
        needsNewEntry = this.wouldRequireNewEntry(this.entries[this.entries.length - 1]);
    }

    if (needsNewEntry) {
        this.spawnNewEntry();
    }
};

bridge.prototype.spawnNewEntry = function (data) {
    // Spawn the entries and then delegate the layout of them to a specialised function.
    var index = this.entries.length;
    var entry = {};

    for (var i in this.controlSpawnSettings) {
        var spawnSettings = this.controlSpawnSettings[i];

        var control = window.rhubarb.spawn(spawnSettings, index, this.presenterPath);
        entry[control.viewBridge.presenterName] = control;
    }

    this.entries[this.entries.length] = entry;

    var entryDom = this.layoutControls(entry);

    this.element.append(entryDom);

    for (i in entry) {
        if (entry[i].viewBridge) {
            entry[i].viewBridge.onRegistered();

            if (data != undefined && data[i] != undefined) {
                entry[i].viewBridge.setValue(data[i]);
            }
        }
    }

    entry.dom = entryDom;
};

/**
 * Returns a dom element of a container that contains the laid out controls.
 *
 * @param controlsCollection
 */
bridge.prototype.layoutControls = function (controlsCollection) {

};

bridge.prototype.onSubPresenterValueChanged = function () {
    this.updateDom();
};

window.rhubarb.viewBridgeClasses.RepeatingCompositeControlViewBridge = bridge;