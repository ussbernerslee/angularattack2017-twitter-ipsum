<h1>Your Ipsums</h1>
<ipsum-create (ipsumCreated)="ipsumUpdate($event)"></ipsum-create>
<ipsum *ngFor="let ipsum of ipsums" [ipsum]="ipsum.ipsum" [twitterUser]="ipsum.twitterUser"></ipsum>