<div class="message-controller" ng-controller="MessagesList" ng-init="init('inbox', <?=($this->gets[1] == 'session' && $this->gets[2] != '')?'true':'false'?>, '<?=$this->_USERDATA['data']['ID']?>', '<?=($this->gets[1] == 'session' && $this->gets[2] != '')?$this->gets[2]:''?>')">
  <div class="wblock">
    <div class="data-container">
      <div class="d-flex">
        <div class="session-list">
          <div class="sess-list">
            <div class="session" ng-repeat="session in sessions" ng-class="{'current': (current_session==session.sessionid), 'archived': (session.archived==1), 'closed': (session.closed==1)}">
              <div class="wrapper" ng-click="changeSession(session.sessionid)">
                <div class="project">
                  <span class="closed" ng-if="session.closed==1" title="<?=__('Lezárt üzenetváltás')?>"><i class="fas fa-lock"></i></span>
                  <span class="archived" ng-if="session.archived==1" title="<?=__('Archivált üzenetváltás')?>"><i class="fas fa-archive"></i></span>
                  <div class="" ng-bind-html="session.messanger_title"></div>
                </div>
                <div class="info">
                  <div>
                    <span class="msg-total">{{session.message_total}} <?=__('db')?> <?=__('üzenet')?></span><span ng-if="session.message_unreaded && session.message_unreaded!=0"> <span class="msg-unreaded">{{session.message_unreaded}} <?=__('olvasatlan')?></span></span>
                  </div>
                </div>
              </div>
            </div>
            <div class="no-data" ng-if="!sessions">
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
            <div class="msg-controlls" ng-if="current_session">
              <div class="archiv">
                <button ng-if="sessions[current_session].archived==0" type="button" ng-click="openArchiver()"><i class="fas fa-archive"> </i> <?=__('Archiválom')?></button>
                <button ng-if="sessions[current_session].archived==1" class="btn-danger" type="button" ng-click="openArchiver()"><i class="fas fa-archive"> </i> <?=__('Archiválva: Módosítás')?></button>
              </div>
              <div class="sep"></div>
              <div class="comment">
                <button type="button" ng-click="openCommentEditor()"><i class="far fa-comment-alt"></i> <?=__('Megjegyzés szerkesztése')?></button>
              </div>
            </div>
            <div class="msg-text" ng-if="sessions[current_session].closed==1">
              <div class="alert alert-danger">
                <i class="fas fa-lock"></i> <?=__('Ez az üzenetváltás le lett zárva. Az üzenetküldés lehetőség nem elérhető.')?>
              </div>
            </div>
            <div class="msg-text" ng-if="sessions[current_session].archived==1">
              <div class="alert alert-warning">
                <i class="fas fa-archive"></i> <?=__('Az archivált üzenetváltásoknál nem kap rendszerszintű e-mail értesítést az olvasatlan üzenetekről.')?>
              </div>
            </div>
            <div class="msg-notice" ng-if="sessions[current_session].notice">
              <label for=""><i class="fas fa-comment-alt"></i> <?=__('Saját privát megjegyzés')?></label>
              <div class="text" ng-bind-html="sessions[current_session].notice|unsafe"></div>
            </div>
            <div class="new-msg" ng-if="current_session && sessions[current_session].closed==0">
              <div class="wrapper">
                <label ng-if="sessions[current_session].relation=='admin'" for="messanger_text"><i class="far fa-envelope"></i> <?=__('Üzenet küldése <strong>{{sessions[current_session].partner_nev}}</strong> részére:')?></label>
                <label ng-if="sessions[current_session].relation=='user'" for="messanger_text"><i class="far fa-envelope"></i> <?=__('Üzenet küldése <strong>Szolgáltatás Közvetítő</strong> részére:')?></label>
                <textarea maxlength="{{newmsg_left_length}}" ng-model="messanger.text" id="messanger_text" class="form-control no-editor"></textarea>
                <small>{{newmsg_left_length}} / {{newmsg_left_length-messanger.text.length}}</small>
                <br>
                <div class="d-flex flex-row justify-content-between align-items-center" ng-if="!newmsg_send_progress">
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
                  <div class="time">{{msg.send_at}}</div>
                </div>
                <div class="author" ng-if="!msg.system_msg">
                  <span class="time">{{msg.send_at}}</span>
                  <span class="name"><span ng-bind-html="msg.from.name|unsafe"></span><span class="readed" ng-if="msg.user_readed_at" title="<?=__('Látta')?>: {{msg.user_readed_at}}"> <i class="fas fa-check-double"></i></span></span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
