<div class="overview admin-view">
  <div class="d-flex">
    <div class="project">
      <div class="head"><?=__('Projekt adatok')?></div>
    </div>
    <div class="requester">
      <div class="head"><?=__('Ajánlatkérő')?></div>
      <div class="cont">
        <div class="chead"><?=__('Személyes adatok')?></div>
        <div value="dpad">
          <table class="table table-bordered">
            <tbody>
              <tr>
                <td><?=__('Projekt azonosító')?></td>
                <td><strong>{{project.requester_project_data.hashkey}}</strong></td>
              </tr>
              <tr>
                <td><?=__('Név / Kapcsolattartó')?></td>
                <td><strong>{{project.user_requester.data.nev}}</strong></td>
              </tr>
              <tr ng-if="project.user_requester.data.company_name">
                <td><?=__('Cég neve')?></td>
                <td><strong>{{project.user_requester.data.company_name}}</strong></td>
              </tr>
              <tr ng-if="project.user_requester.data.company_adoszam">
                <td><?=__('Adószám')?></td>
                <td><strong>{{project.user_requester.data.company_adoszam}}</strong></td>
              </tr>
              <tr>
                <td><?=__('E-mail cím')?></td>
                <td><strong>{{project.user_requester.data.email}}</strong></td>
              </tr>
              <tr ng-if="project.user_requester.data.szallitas_phone">
                <td><?=__('Telefonszám')?></td>
                <td><strong>{{project.user_requester.data.szallitas_phone}}</strong></td>
              </tr>
              <tr>
                <td><?=__('Számlázási név')?></td>
                <td><strong>{{project.user_requester.szamlazasi_adat.nev}}</strong></td>
              </tr>
              <tr>
                <td><?=__('Számlázási cím')?></td>
                <td><strong>{{project.user_requester.szamlazasi_adat.irsz}} {{project.user_requester.szamlazasi_adat.city}}, {{project.user_requester.szamlazasi_adat.kozterulet_nev}} {{project.user_requester.szamlazasi_adat.kozterulet_jelleg}} {{project.user_requester.szamlazasi_adat.hazszam}}.</strong><br>
                  <span ng-if="project.user_requester.szamlazasi_adat.kerulet"><?=__('Kerület')?>: {{project.user_requester.szamlazasi_adat.kerulet}}</span>
                  <span ng-if="project.user_requester.szamlazasi_adat.epulet"><?=__('Épület')?>: {{project.user_requester.szamlazasi_adat.epulet}}</span>
                  <span ng-if="project.user_requester.szamlazasi_adat.lepcsohaz"><?=__('Lépcsőház')?>: {{project.user_requester.szamlazasi_adat.lepcsohaz}}</span>
                  <span ng-if="project.user_requester.szamlazasi_adat.szint"><?=__('Szint')?>: {{project.user_requester.szamlazasi_adat.szint}}</span>
                  <span ng-if="project.user_requester.szamlazasi_adat.ajto"><?=__('Ajtó')?>: {{project.user_requester.szamlazasi_adat.ajto}}</span>
                </td>
              </tr>
              <tr>
                <td><?=__('Szállítási cím')?></td>
                <td><strong>{{project.user_requester.szallitasi_adat.irsz}} {{project.user_requester.szallitasi_adat.city}}, {{project.user_requester.szallitasi_adat.kozterulet_nev}} {{project.user_requester.szallitasi_adat.kozterulet_jelleg}} {{project.user_requester.szallitasi_adat.hazszam}}.</strong><br>
                  <span ng-if="project.user_requester.szallitasi_adat.kerulet"><?=__('Kerület')?>: {{project.user_requester.szallitasi_adat.kerulet}}</span>
                  <span ng-if="project.user_requester.szallitasi_adat.epulet"><?=__('Épület')?>: {{project.user_requester.szallitasi_adat.epulet}}</span>
                  <span ng-if="project.user_requester.szallitasi_adat.lepcsohaz"><?=__('Lépcsőház')?>: {{project.user_requester.szallitasi_adat.lepcsohaz}}</span>
                  <span ng-if="project.user_requester.szallitasi_adat.szint"><?=__('Szint')?>: {{project.user_requester.szallitasi_adat.szint}}</span>
                  <span ng-if="project.user_requester.szallitasi_adat.ajto"><?=__('Ajtó')?>: {{project.user_requester.szallitasi_adat.ajto}}</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="chead"><?=__('Ajánlat részletei')?></div>
        <div class="dpad wpadding infoline">
          <label for=""><?=__('Ajánlat kiküldve')?>:</label>
          <div class="pval">{{project.requester_project_data.offer_data.sended_at_dist}} ({{project.requester_project_data.offer_data.sended_at}})</div>

          <label for=""><?=__('Ajánlat elfogadva - Ajánlatkérő által')?>:</label>
          <div class="pval">{{project.requester_project_data.offer_data.accepted_at_dist}} ({{project.requester_project_data.offer_data.accepted_at}})</div>

          <label for=""><?=__('Szolgáltatás díja - Ajánlatkérő fizet')?>:</label>
          <div class="pval">{{project.requester_project_data.offer_data.price|cash}}</div>

          <label for=""><?=__('Ajánlat részletei')?>:</label>
          <div class="pval" ng-bind-html="project.requester_project_data.offer_data.message|unsafe"></div>
        </div>
        <div class="chead"><?=__('Dokumentumok')?></div>
        <div class="chead"><?=__('Üzenetek')?></div>
      </div>
    </div>
    <div class="servicer">
      <div class="head"><?=__('Szolgáltató')?></div>
      <div class="cont">
        <div class="chead"><?=__('Személyes adatok')?></div>
        <div value="dpad">
          <table class="table table-bordered">
            <tbody>
              <tr>
                <td><?=__('Projekt azonosító')?></td>
                <td><strong>{{project.servicer_project_data.hashkey}}</strong></td>
              </tr>
              <tr>
                <td><?=__('Név / Kapcsolattartó')?></td>
                <td><strong>{{project.user_servicer.data.nev}}</strong></td>
              </tr>
              <tr ng-if="project.user_servicer.data.company_name">
                <td><?=__('Cég neve')?></td>
                <td><strong>{{project.user_servicer.data.company_name}}</strong></td>
              </tr>
              <tr ng-if="project.user_servicer.data.company_adoszam">
                <td><?=__('Adószám')?></td>
                <td><strong>{{project.user_servicer.data.company_adoszam}}</strong></td>
              </tr>
              <tr>
                <td><?=__('E-mail cím')?></td>
                <td><strong>{{project.user_servicer.data.email}}</strong></td>
              </tr>
              <tr ng-if="project.user_servicer.data.szallitas_phone">
                <td><?=__('Telefonszám')?></td>
                <td><strong>{{project.user_servicer.data.szallitas_phone}}</strong></td>
              </tr>
              <tr>
                <td><?=__('Számlázási név')?></td>
                <td><strong>{{project.user_servicer.szamlazasi_adat.nev}}</strong></td>
              </tr>
              <tr>
                <td><?=__('Számlázási cím')?></td>
                <td><strong>{{project.user_servicer.szamlazasi_adat.irsz}} {{project.user_servicer.szamlazasi_adat.city}}, {{project.user_servicer.szamlazasi_adat.kozterulet_nev}} {{project.user_servicer.szamlazasi_adat.kozterulet_jelleg}} {{project.user_servicer.szamlazasi_adat.hazszam}}.</strong><br>
                  <span ng-if="project.user_servicer.szamlazasi_adat.kerulet"><?=__('Kerület')?>: {{project.user_servicer.szamlazasi_adat.kerulet}}</span>
                  <span ng-if="project.user_servicer.szamlazasi_adat.epulet"><?=__('Épület')?>: {{project.user_servicer.szamlazasi_adat.epulet}}</span>
                  <span ng-if="project.user_servicer.szamlazasi_adat.lepcsohaz"><?=__('Lépcsőház')?>: {{project.user_servicer.szamlazasi_adat.lepcsohaz}}</span>
                  <span ng-if="project.user_servicer.szamlazasi_adat.szint"><?=__('Szint')?>: {{project.user_servicer.szamlazasi_adat.szint}}</span>
                  <span ng-if="project.user_servicer.szamlazasi_adat.ajto"><?=__('Ajtó')?>: {{project.user_servicer.szamlazasi_adat.ajto}}</span>
                </td>
              </tr>
              <tr>
                <td><?=__('Szállítási cím')?></td>
                <td><strong>{{project.user_servicer.szallitasi_adat.irsz}} {{project.user_servicer.szallitasi_adat.city}}, {{project.user_servicer.szallitasi_adat.kozterulet_nev}} {{project.user_servicer.szallitasi_adat.kozterulet_jelleg}} {{project.user_servicer.szallitasi_adat.hazszam}}.</strong><br>
                  <span ng-if="project.user_servicer.szallitasi_adat.kerulet"><?=__('Kerület')?>: {{project.user_servicer.szallitasi_adat.kerulet}}</span>
                  <span ng-if="project.user_servicer.szallitasi_adat.epulet"><?=__('Épület')?>: {{project.user_servicer.szallitasi_adat.epulet}}</span>
                  <span ng-if="project.user_servicer.szallitasi_adat.lepcsohaz"><?=__('Lépcsőház')?>: {{project.user_servicer.szallitasi_adat.lepcsohaz}}</span>
                  <span ng-if="project.user_servicer.szallitasi_adat.szint"><?=__('Szint')?>: {{project.user_servicer.szallitasi_adat.szint}}</span>
                  <span ng-if="project.user_servicer.szallitasi_adat.ajto"><?=__('Ajtó')?>: {{project.user_servicer.szallitasi_adat.ajto}}</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="chead"><?=__('Ajánlat részletei')?></div>
        <div class="dpad wpadding infoline">
          <label for=""><?=__('Ajánlat beérkezett')?>:</label>
          <div class="pval">{{project.servicer_project_data.offer_data.sended_at_dist}} ({{project.servicer_project_data.offer_data.sended_at}})</div>

          <label for=""><?=__('Ajánlat elfogadva - Közvetítő által')?>:</label>
          <div class="pval">{{project.servicer_project_data.offer_data.accepted_at_dist}} ({{project.servicer_project_data.offer_data.accepted_at}})</div>

          <label for=""><?=__('Szolgáltatás díja - Közvetítő fizet')?>:</label>
          <div class="pval">{{project.servicer_project_data.offer_data.price|cash}}</div>

          <label for=""><?=__('Ajánlat részletei')?>:</label>
          <div class="pval" ng-bind-html="project.servicer_project_data.offer_data.message|unsafe"></div>
        </div>
      </div>
    </div>
  </div>
</div>
