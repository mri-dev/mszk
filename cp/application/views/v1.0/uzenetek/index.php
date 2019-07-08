<div class="message-controller" ng-controller="MessagesList" ng-init="init('inbox', false, '<?=$this->_USERDATA['data']['ID']?>', '')">
  <div class="wblock">
    <div class="data-container">
      <div class="d-flex">
        <div class="session-list">
          <div class="sess-list">
            <div class="session" ng-repeat="session in sessions" ng-class="{'current': (current_session==session.sessionid)}">
              <div class="wrapper" ng-click="changeSession(session.sessionid)">
                <div class="project">
                  {{session.project_title}}
                </div>
                <div class="info">
                  <div>
                    <span class="msg-total">{{session.message_total}} <?=__('db')?> <?=__('üzenet')?></span><span ng-if="session.message_unreaded && session.message_unreaded!=0"> <span class="msg-unreaded">{{session.message_unreaded}} <?=__('olvasatlan')?></span></span>
                  </div>
                </div>
              </div>
            </div>
            <div class="no-requests" ng-if="!sessions">
              <div class="wrapper">
                <i class="far fa-folder-open"></i><?=__('Nincs elérhető üzenetváltás.')?>
              </div>
            </div>
          </div>
        </div>
        <div class="session-data">
          <div class="no-data" ng-if="current_session==''">
            <h4><?=__('Az üzenetek megtekintéséhez válasszon a bal oldali beszélgetések közül!')?></h4>
            <div class="unreaded-msg" ng-if="unreaded && unreaded!=0">
              {{unreaded}} <?=__('db olvasatlan üzenete van')?>
            </div>
          </div>
          <div class="messages" ng-if="messages">
            <div class="new-msg" ng-if="current_session">
              <div class="wrapper">
                <label for="messanger_text"><i class="far fa-envelope"></i> <?=__('Üzenet küldése <strong>{{partner.data.nev}}</strong> részére:')?></label>
                <textarea ng-model="messanger.text" id="messanger_text" class="form-control no-editor"></textarea>
                <br>
                <div class="d-flex flex-row justify-content-between align-items-center">
                  <div class=""></div>
                  <div class="">
                    <button type="button" class="btn btn-primary btn-sm" ng-click="sendMessage()"><?=__('Üzenet küldése')?> <i class="fas fa-paper-plane"></i></button>
                  </div>
                </div>
              </div>
            </div>
            <div class="no-msg" ng-if="messages.length==0 &&  current_session!=''">
              <i class="far fa-envelope-open"></i><br>
              <?=__('Még nem történt üzenetváltás!')?>
            </div>
            <div class="msg" ng-repeat="msg in messages.msg" ng-class="{'from_me': msg.from_me, 'unreaded': (!msg.user_readed_at), 'systemmsg': msg.system_msg }">
              <div class="wrapper">
                <div class="message-text" ng-bind-html="msg.msg|unsafe"></div>
                <div class="system-msg-head" ng-if="msg.system_msg">
                  <?=__('Rendszerüzenet')?>
                </div>
                <div class="author" ng-if="!msg.system_msg">
                  <span class="time">{{msg.send_at}}</span>
                  <span class="name">{{msg.from.name}}<span class="readed" ng-if="msg.user_readed_at" title="<?=__('Látta')?>: {{msg.user_readed_at}}"> <i class="fas fa-check-double"></i></span></span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
